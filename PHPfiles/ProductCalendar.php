<?php
include "HtmlHelpers.php";
include "Constants.php";

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

    private static function renderNoLunchTableCell($productDate) {
        $dateString = date_format(new DateTime($productDate), "M d, Y");
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
            if (time() > $orderTooLateDateTime) {
                update_post_meta($productId, 'timing_key', ProductOrderTiming::TooLate);
                return ProductOrderTiming::TooLate;
            }
            return ProductOrderTiming::KindaLate;
        } 
        
        // product currently marked ontime, check if it's kindda late, or too late
        if (time() < $orderLateDateTime) {
            return ProductOrderTiming::OnTime;
        } elseif (time() < $orderTooLateDateTime) {
            // update product timing state
            update_post_meta($productId, 'timing_key', ProductOrderTiming::KindaLate);

            // update product's inventory count
            if ($productDetails->get_stock_quantity() > 50) {
                wc_update_product_stock($productId, 50);
            }

            //Get and set new price for each variation of the parent product
			$mealVariationIds = $productDetails->get_visible_children();
            
            foreach ($mealVariationIds as $currentVariationId) {
                $productVariation = wc_get_product($currentVariationId);
                $RegPrice = $productVariation->get_regular_price();
                $lateprice = $RegPrice + 1;
                update_post_meta($currentVariationId, '_regular_price', $lateprice);
                wc_delete_product_transients($currentVariationId);
            }
            return ProductOrderTiming::KindaLate;
        } else {
            update_post_meta($productId, 'timing_key', ProductOrderTiming::TooLate);
            return ProductOrderTiming::TooLate;
        }
    }

    private static function getTooLateOrderDeadline($deliveryDateTime) {
        //TODO: some cleanup

        //Account for weekends
		if (date('w',$deliveryDateTime) < 3) {
			$changedeadline = ($deliveryDateTime-302400);
		} else {
			$changedeadline = ($deliveryDateTime-129600);
		}
					
		//Set holidays
		$holidays = array("2017-11-23","2017-12-25","2017-12-24","2018-01-01");
					
		//Subtract a day for the holidays
		foreach($holidays as $holiday){
			$time_stamp=strtotime($holiday);
		
			//If the holiday doesn't fall in weekend, move back change deadline by a day
			if ($changedeadline <= $time_stamp && $time_stamp <= $deliveryDateTime && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7) {
				$changedeadline = $changedeadline-86400;
			}
        }
        
        return $changedeadline;
    }

    private static function getLateOrderDeadline($deliveryDateTime) {
        //find week of initial order deadline
        $deadlineweek = (date('W',$deliveryDateTime)-1);
        
        //set day of initial order deadline
        $deadline = new DateTime();
        $deadline->setISODate(date("Y"), $deadlineweek);

        //set time of initial order deadline
        $deadline->setTime(36, 00);

        return $deadline;
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
            if (meal_in_cart($productId)) {
                remove_expired_meal_from_cart($productId);                
            }
            HtmlHelpers::writeParagraphStartTag("text-align:center;");
            HtmlHelpers::writeInItalics("Ordering expired");
            HtmlHelpers::writeParagraphEndTag();
            HtmlHelpers::writeBreakLine();
        } elseif (meal_already_bought($productId) || meal_in_cart($productId)) {
            HtmlHelpers::writeParagraphStartTag("text-align:center;");
            HtmlHelpers::writeAnchor($productDetails->get_permalink() . "?childId=$childId", "View details", "color: #9296A1;");
            HtmlHelpers::writeParagraphEndTag();
        } else {
            HtmlHelpers::writeParagraphStartTag("text-align:center;");
            HtmlHelpers::writeAnchorStartTag($productDetails->get_permalink() . "?childId=$childId");
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
                HtmlHelpers::writeInItalics(date('D, M d', $orderTooLateDateTime) . " at noon");
                HtmlHelpers::writeParagraphEndTag();    
            }
        }
        
        HtmlHelpers::writeTableCellEndTag();
    }
}
?>
