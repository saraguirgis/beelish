<?php
include "HtmlHelpers.php";
include "Constants.php";

class ProductCalendar {
    function ProductCalendar() {
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
        HtmlHelpers::writeTableCell("Sept 11, 2017 <p><i>No lunch</i></p>", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableCell("Sept 12, 2017 <p><i>No lunch</i></p>", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableCell("Sept 13, 2017 <p><i>No lunch</i></p>", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableCell("Sept 14, 2017 <p><i>No lunch</i></p>", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableCell("Sept 15, 2017 <p><i>No lunch</i></p>", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableRowEndTag();

        HtmlHelpers::writeTableRowStartTag();
        HtmlHelpers::writeTableCell("Sept 18, 2017 <p><i>No lunch</i></p>", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableCell("Sept 19, 2017 <p><i>No lunch</i></p>", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableCell("Sept 20, 2017 <p><i>No lunch</i></p>", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableCell("Sept 21, 2017 <p><i>No lunch</i></p>", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableCell("Sept 22, 2017 <p><i>No lunch</i></p>", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableRowEndTag();

        HtmlHelpers::writeTableRowStartTag();
        display_meal(170);
        display_meal(167);
        display_meal(159);
        display_meal(389);
        display_meal(392);
        HtmlHelpers::writeTableRowEndTag();

        HtmlHelpers::writeTableRowStartTag();
        display_meal(395);
        display_meal(401);
        display_meal(401);		
        display_meal(437);		
        display_meal(401);		        
        HtmlHelpers::writeTableRowEndTag();
        
        HtmlHelpers::writeTableRowStartTag();
        display_meal(401);
        display_meal(401);
        HtmlHelpers::writeTableCell("&nbsp;", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableCell("&nbsp;", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableCell("&nbsp;", ThemeConstants::TableCellNothingToOrderStyle);
        HtmlHelpers::writeTableRowEndTag();

        HtmlHelpers::writeTableEndTag();
    }
}

?>
