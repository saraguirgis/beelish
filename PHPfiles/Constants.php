<?php
/**
 * A class to hold Theme related constants.
 */
class ThemeConstants {
    /** Table header background color. */
    const TableCellHeaderBGColor = "#D9EDF7";
    /** Table cell background color when there's no lunch available or ordering is expired. */
    const TableCellNothingToOrderBGColor = "#D3D3D3";
    /** Table cell background color when lunch is available to order. */
    const TableCellLunchAvailableBGColor = "#FFFFFF";
    /** Table cell width in percentage. */
    const TableCellWidthPercentage = "20%";
    /** Table cell height in pixels. */
    const TableCellHeightPixels = "100px";
    /** Table cell header style. */
    const TableCellHeaderStyle = "width: " . ThemeConstants::TableCellWidthPercentage . "; background-color: " . ThemeConstants::TableCellHeaderBGColor . ";";
    /** Table cell style when there's no lunch available or oderering is expired. */
    const TableCellNothingToOrderStyle = "width: " . ThemeConstants::TableCellWidthPercentage . "; height: " . ThemeConstants::TableCellHeightPixels . "; background-color: " . ThemeConstants::TableCellNothingToOrderBGColor . ";";
}
?>