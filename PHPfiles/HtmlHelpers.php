<?php
/**
 * A class containing HTML helpers.
 */
class HtmlHelpers {
    /**
     * Renders a break line in HTML.
     */
    public static function writeBreakLine() {
        echo "<BR />\r\n";
    }

    /**
     * Writes an HTML table cell.
     * @param string $cellContent Contents of the cell.
     * @param string $style (Optional) Table cell style.
     */
    public static function writeTableCell($cellContent, $style = null) {
        echo "<td";

        // write style contents if provided
        if ($style !== null) {
            echo " style=\"$style\"";
        }
        ">$cellContent</td>\r\n";
    }
    
    /**
     * Writes text in italics.
     * @param string $content Text content to be written in italics.
     */
    public static function writeInItalics($content) {
        echo "<i>$content</i>";
    }

    /**
     * Writes Table row start tag.
     */
     public static function writeTableRowStartTag() {
         echo "<tr>\r\n";
     }

    /**
     * Writes Table row end tag.
     */
     public static function writeTableRowEndTag() {
        echo "</tr>\r\n";
    }
}
?>