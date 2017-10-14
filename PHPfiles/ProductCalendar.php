<?php
include "HtmlHelpers.php";
include "TimeHelpers.php";
//include "Constants.php"; not included here since included in the functions file

class ProductCalendar {
    const NoLunchProductId = -1;
    const NoDetailsProductId = null;

    /**
     * Renders the product calendar with the given products for a given childId
     * @param string $calendarTitle Calendar month title (eg October 2017).
     * @param array $productValues An ordered array (map) containing all the products where
     *                             the key is the offer date, and value is the parent products id.
     * @param int $childId The child Id for which to order products for.
     */
    function __construct($calendarTitle, $productValues, $childId) {
        //sets default timezone
		date_default_timezone_set('America/Los_Angeles');
        
        // initialize class variables
        $this->calendarTitle = $calendarTitle;
        $this->products = $productValues;
        $this->childId = $childId;
    }

    function renderCalendar() {
        // Write month title
        HtmlHelpers::writeH2($this->calendarTitle);

        HtmlHelpers::writeTableStartTag("color: #000000; vertical-align: top;");

        // table header row
        HtmlHelpers::writeTableRowStartTag();
        HtmlHelpers::writeTableHeaderCell("Monday",    ThemeConstants::TableCellHeaderStyle);
        HtmlHelpers::writeTableHeaderCell("Tuesday",   ThemeConstants::TableCellHeaderStyle);
        HtmlHelpers::writeTableHeaderCell("Wednesday", ThemeConstants::TableCellHeaderStyle);
        HtmlHelpers::writeTableHeaderCell("Thursday",  ThemeConstants::TableCellHeaderStyle);
        HtmlHelpers::writeTableHeaderCell("Friday",    ThemeConstants::TableCellHeaderStyle);
        HtmlHelpers::writeTableRowEndTag();

        HtmlHelpers::writeTableRowStartTag();

        $columnNumber = 0;
        foreach ($this->products as $productDate => $productId) {

            // end table row and start a new one 
            if ($columnNumber > 4) {
                HtmlHelpers::writeTableRowEndTag();
                HtmlHelpers::writeTableRowStartTag();
                $columnNumber = 0;
            }
            if ($productId === ProductCalendar::NoLunchProductId) {
                ProductCalendar::renderNoLunchTableCell($productDate);
            } elseif ($productId === ProductCalendar::NoDetailsProductId) {
                ProductCalendar::renderNoProductDetailsTableCell();
            } else {
                $productDetails = wc_get_product($productId);

                if (BusinessConfigs::ResetProductState) {
                  ProductCalendar::resetProductState($productId, $productDetails);
                }

                $orderLateDateTime = ProductCalendar::getLateOrderDeadline(strtotime($productDetails->get_SKU()));
                $orderTooLateDateTime = ProductCalendar::getTooLateOrderDeadline(strtotime($productDetails->get_SKU()));        
                $productTimingKey = ProductCalendar::updateProductStateForTiming($productId, $productDetails, $orderLateDateTime, $orderTooLateDateTime);
                ProductCalendar::renderProductTableCell($productId, $productDetails, $productTimingKey, $orderTooLateDateTime, $this->childId);
            }

            $columnNumber++;
        }

        HtmlHelpers::writeTableRowEndTag();
        HtmlHelpers::writeTableEndTag();
        HtmlHelpers::writeBreakLine();
        HtmlHelpers::writeBreakLine();        
    }

    private static function resetProductState($productId, $productDetails) {
        update_post_meta($productId, 'timing_key', ProductOrderTiming::OnTime);

        // reset default inventory count
        wc_update_product_stock($productId, BusinessConfigs::DefaultProductStockQuantity);
    }

    private static function renderNoLunchTableCell($productDate) {
        $dateString = date_format(new DateTime($productDate), "M d");
        HtmlHelpers::writeTableCell("$dateString <p><i>No lunch</i></p>", ThemeConstants::TableCellNothingToOrderStyle);
    }

    private static function renderNoProductDetailsTableCell() {
        HtmlHelpers::writeTableCell("&nbsp;", ThemeConstants::TableCellNothingToOrderStyle);                
    }

    private static function updateProductStateForTiming($productId, $productDetails, $orderLateDateTime, $orderTooLateDateTime) {
        $productTimingKey = get_post_meta($productId, 'timing_key', true);

        if ($productTimingKey == ProductOrderTiming::TooLate) {
            // nothing to do, product already marked as too late
            return ProductOrderTiming::TooLate;
        }

        if ($productTimingKey == ProductOrderTiming::KindaLate) {
            // check if it's too late to order
            if (time() > $orderTooLateDateTime->getTimestamp()) {
                update_post_meta($productId, 'timing_key', ProductOrderTiming::TooLate);
                return ProductOrderTiming::TooLate;
            }
            return ProductOrderTiming::KindaLate;
        } 
        
        // product currently marked ontime, check if it's kindda late, or too late
        if (time() < $orderLateDateTime->getTimestamp()) {
            return ProductOrderTiming::OnTime;
        } elseif (time() < $orderTooLateDateTime->getTimestamp()) {
            // update product timing state
            update_post_meta($productId, 'timing_key', ProductOrderTiming::KindaLate);

            // update product's inventory count
            if ($productDetails->get_stock_quantity() > BusinessConfigs::ChangeWindowStockQuantity) {
                wc_update_product_stock($productId, BusinessConfigs::ChangeWindowStockQuantity);
            }

            //Get and set new price for each variation of the parent product
			$mealVariationIds = $productDetails->get_visible_children();
            
            foreach ($mealVariationIds as $currentVariationId) {
                $productVariation = wc_get_product($currentVariationId);
                $RegPrice = $productVariation->get_regular_price();
                $lateprice = $RegPrice + BusinessConfigs::LatePenaltyChargeInDollars;
                update_post_meta($currentVariationId, '_regular_price', $lateprice);
                wc_delete_product_transients($currentVariationId);
            }
            return ProductOrderTiming::KindaLate;
        } else {
            update_post_meta($productId, 'timing_key', ProductOrderTiming::TooLate);
            return ProductOrderTiming::TooLate;
        }
    }

    private static function getTooLateOrderDeadline($deliveryTimestamp) {
        
        $businessDaysToSubtract = BusinessConfigs::ChangesDeadlineInBusinessDays;

        $resultDeadlineTimestamp = $deliveryTimestamp;

        while ($businessDaysToSubtract > 0) {
            $resultDeadlineTimestamp = strtotime("yesterday", $resultDeadlineTimestamp);

            // only make it count if it was a business day
            if (!TimeHelpers::isWeekend($resultDeadlineTimestamp)
             && !TimeHelpers::isHoliday(BusinessConfigs::Holidays, $resultDeadlineTimestamp)) {
                $businessDaysToSubtract--;
            }
        }

        // set time to noon
        $resultDateTime = DateTime::createFromFormat('U', $resultDeadlineTimestamp);
        $resultDateTime->setTime(12, 00);

        return $resultDateTime;
    }

    private static function getLateOrderDeadline($deliveryTimestamp) {

        $resultTimestamp = $deliveryTimestamp;        

        // if Wed-Friday, go to the previous tuesday which would be in the current week
        if (date('w', $deliveryTimestamp) >= TimeHelpers::Wednesday) {
            $resultTimestamp = strtotime("last Tuesday", $deliveryTimestamp);
        }
        
        // go to Tuesday of previous week
        $resultTimestamp = strtotime("last Tuesday", $resultTimestamp);

        // set time to noon
        $resultDateTime = DateTime::createFromFormat('U', $resultTimestamp);
        $resultDateTime->setTime(12, 00);

        return $resultDateTime;
    }

    private static function renderProductTableCell($productId, $productDetails, $productTimingKey, $orderTooLateDateTime, $childId) {
        // set table cell style based on product timing key
        $tableCellStyle = ($productTimingKey == ProductOrderTiming::TooLate) ?
            ThemeConstants::TableCellNothingToOrderStyle :
            ThemeConstants::TableCellDefaultStyle;

        HtmlHelpers::writeTableCellStartTag($tableCellStyle);

        // always show product image/title
        echo $productDetails->get_image(array(80, 128));
        HtmlHelpers::writeBreakLine();

        echo $productDetails->get_title();
        HtmlHelpers::writeBreakLine();
        HtmlHelpers::writeBreakLine();

        if ($productTimingKey == ProductOrderTiming::TooLate) {
            remove_expired_meal_from_cart($productId);

            HtmlHelpers::writeParagraphStartTag("text-align:center;");
            HtmlHelpers::writeInItalics("Ordering expired");
            HtmlHelpers::writeParagraphEndTag();
            HtmlHelpers::writeBreakLine();
        // commenting this out to remove "meal already bought" part since with multiple children we want them to be able to purchase again even if it was already bought
		//} elseif (meal_already_bought($productId) || meal_in_cart($productId)) {
		} elseif (meal_in_cart($productId)) {
            HtmlHelpers::writeParagraphStartTag("text-align:center;");
            HtmlHelpers::writeAnchor($productDetails->get_permalink(), "View details", "color: #9296A1;");
            HtmlHelpers::writeParagraphEndTag();
        } else {
            HtmlHelpers::writeParagraphStartTag("text-align:center;");
            HtmlHelpers::writeAnchorStartTag($productDetails->get_permalink());
            echo "<i class=\"fa fa-cutlery\" aria-hidden=\"true\"></i>&nbsp;Order";
            HtmlHelpers::writeAnchorEndTag();
            HtmlHelpers::writeBreakLine();
            HtmlHelpers::writeParagraphEndTag();

            // display notice for late ordering
            if ($productTimingKey == ProductOrderTiming::KindaLate) {
                HtmlHelpers::writeParagraphStartTag("color: #FF6600;");
                echo "<i class=\"fa fa-clock-o fa-lg\" aria-hidden=\"true\"></i>&nbsp;";
                HtmlHelpers::writeInItalics("Order last minute until");
                HtmlHelpers::writeBreakLine();
                HtmlHelpers::writeInItalics(date('D, M d', $orderTooLateDateTime->getTimestamp()) . " at noon");
                HtmlHelpers::writeParagraphEndTag();    
            }
        }

        /* commenting this out to remove "meal already bought" part since with multiple children we want them to be able to purchase again even if it was already bought
		if (meal_already_bought($productId)) {
    		$current_user = wp_get_current_user();

            echo '<div class="user-bought"><i>&checkmark; ' . $current_user->child1_name . ' ordered this.</i></div>';

		} */
		
		//Display who the meal was already purchased for if it was purchased
		$alreadyorderedforname = names_meal_ordered_for($productId);
	
	if ($alreadyorderedforname != NULL) {
		foreach ($alreadyorderedforname as $key => $val) {
			echo "<div class=\"user-bought\"><i class=\"fa fa-calendar-check-o\" aria-hidden=\"true\"></i> Purchased for " . $val . "</div>";
		}	
	}
		
		
        if (meal_in_cart($productId)) {
            // display note if meal is in the cart
            echo '<div class="user-bought"><mark><i class="fa fa-shopping-cart" aria-hidden="true"></i> Added to cart. Please <a href="https://www.beelish.com/cart/"><b>check out</b></a> to complete order.</mark></div>';
        }
        
        HtmlHelpers::writeTableCellEndTag();
    }
}
?>
