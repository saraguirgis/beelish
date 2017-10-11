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
     * Writes an HTML paragraph start tag.
     * @param string $style (Optional) Paragraph style.
     */
    public static function writeParagraphStartTag($style = null) {
        echo "<p";

        // write style contents if provided
        if ($style !== null) {
            echo " style=\"$style\"";
        }
        echo ">\r\n";
    }

    /**
     * Writes an HTML paragraph end tag.
     */
    public static function writeParagraphEndTag() {
        echo "</p>\r\n";
    }

    /**
     * Writes an HTML anchor start tag.
     * @param string $linkRef Address of link.
     * @param string $style (Optional) Anchcor style.
     */
    public static function writeAnchorStartTag($linkRef, $style = null) {
        echo "<a";

        // write style contents if provided
        if ($style !== null) {
            echo " style=\"$style\"";
        }
        echo " href=\"$linkRef\">";
    }

    /**
     * Writes an HTML anchor end tag.
     */
     public static function writeAnchorEndTag() {
        echo "</a>";
    }

    /**
     * Writes an HTML anchor.
     * @param string $linkRef Address of link.
     * @param string $title Anchor title/text.
     * @param string $style (Optional) Anchcor style.
     */
    public static function writeAnchor($linkRef, $title, $style = null) {
        HtmlHelpers::writeAnchorStartTag($linkRef, $style);
        echo $title;
        HtmlHelpers::writeAnchorEndTag();
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
        echo ">$cellContent</th>\r\n";
    }

    /**
     * Writes an HTML table cell start tag.
     * @param string $style (Optional) Table cell style.
     */
    public static function writeTableCellStartTag($style = null) {
        echo "<td";

        // write style contents if provided
        if ($style !== null) {
            echo " style=\"$style\"";
        }
        echo ">\r\n";
    }

    /**
     * Writes an HTML table cell end tag.
     */
    public static function writeTableCellEndTag() {
        echo "</td>\r\n";
    }

    /**
     * Writes an HTML table cell.
     * @param string $cellContent Contents of the cell.
     * @param string $style (Optional) Table cell style.
     */
    public static function writeTableCell($cellContent, $style = null) {
        HtmlHelpers::writeTableCellStartTag($style);
        echo $cellContent;
        HtmlHelpers::writeTableCellEndTag();
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
