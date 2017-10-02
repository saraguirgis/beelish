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
     * Writes an HTML table header cell.
     * @param string $cellContent Contents of the cell.
     * @param string $style (Optional) Table cell style.
     */
     public static function writeTableHeaderCell($cellContent, $style = null) {
        echo "<th";

        // write style contents if provided
        if ($style !== null) {
            echo " style=\"$style\"";
        }
        ">$cellContent</th>\r\n";
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
     * Writes a header 2 text.
     * @param string $content Text content to be written in header 2.
     */
     public static function writeH2($content) {
        echo "<h2>$content</h2>\r\n";
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

    /**
     * Writes Table start tag.
     * @param string $style (Optional) Table style.
     */
     public static function writeTableStartTag($style = null) {
        echo "<table";

        // write style contents if provided
        if ($style !== null) {
            echo " style=\"$style\"";
        }
        echo ">\r\n";
    }

   /**
    * Writes Table end tag.
    */
    public static function writeTableEndTag() {
       echo "</table>\r\n";
   }

}
?>