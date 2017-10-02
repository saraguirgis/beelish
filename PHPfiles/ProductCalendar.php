<?php
include "HtmlHelpers.php";
include "Constants.php";

class ProductCalendar {
    const NoLunchProductId = -1;
    const NoDetailsProductId = null;
    function ProductCalendar() {
        // Initialize product dates and IDs array
        $products = array(
            "09/11/2017" => ProductCalendar::NoLunchProductId,
            "09/12/2017" => ProductCalendar::NoLunchProductId,
            "09/13/2017" => ProductCalendar::NoLunchProductId,
            "09/14/2017" => ProductCalendar::NoLunchProductId,
            "09/15/2017" => ProductCalendar::NoLunchProductId,
            
            "09/18/2017" => ProductCalendar::NoLunchProductId,
            "09/19/2017" => ProductCalendar::NoLunchProductId,
            "09/20/2017" => ProductCalendar::NoLunchProductId,
            "09/21/2017" => ProductCalendar::NoLunchProductId,
            "09/22/2017" => ProductCalendar::NoLunchProductId,
            
            "09/25/2017" => 170,
            "09/26/2017" => 167,
            "09/27/2017" => 159,
            "09/28/2017" => 389,
            "09/29/2017" => 392,
            
            "10/02/2017" => 395,
            "10/03/2017" => 401,
            "10/04/2017" => 401,
            "10/05/2017" => 437,
            "10/06/2017" => 401,
            
            "10/09/2017" => 401,
            "10/10/2017" => 401,
            "10/11/2017" => ProductCalendar::NoDetailsProductId,
            "10/12/2017" => ProductCalendar::NoDetailsProductId,
            "10/13/2017" => ProductCalendar::NoDetailsProductId,
            
        );
    }

    function renderCalendar() {

        HtmlHelpers::writeH2("October 2017");

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
                $dateString = date_format("F d, Y", new DateTime($productDate));
                HtmlHelpers::writeTableCell("$dateString <p><i>No lunch</i></p>", ThemeConstants::TableCellNothingToOrderStyle);
            } elseif ($productId === ProductCalendar::NoDetailsProductId) {
                HtmlHelpers::writeTableCell("&nbsp;", ThemeConstants::TableCellNothingToOrderStyle);                
            } else {
                display_meal($productId);
            }

            $columnNumber++;
        }

        HtmlHelpers::writeTableRowEndTag();
        HtmlHelpers::writeTableEndTag();
    }
}

?>
