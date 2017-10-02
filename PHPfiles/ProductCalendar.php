<?php
include "HtmlHelpers.php";
include "Constants.php";

class ProductCalendar {
    const NoLunchProductId = -1;
    const NoDetailsProductId = null;

    function __construct($calendarTitle, $productValues) {
        $this->calendarTitle = $calendarTitle;
        $this->products = $productValues;
    }

    function renderCalendar() {

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
                $dateString = date_format(new DateTime($productDate), "M d, Y");
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
        HtmlHelpers::writeBreakLine();
        HtmlHelpers::writeBreakLine();        
    }
}

?>
