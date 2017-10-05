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
    /** Default Table cell style. */
    const TableCellDefaultStyle = "vertical-align: top; width: " . ThemeConstants::TableCellWidthPercentage . "; height: " . ThemeConstants::TableCellHeightPixels . "; background-color: " . ThemeConstants::TableCellLunchAvailableBGColor . ";";
    /** Table cell style when there's no lunch available or oderering is expired. */
    const TableCellNothingToOrderStyle = "vertical-align: top; width: " . ThemeConstants::TableCellWidthPercentage . "; height: " . ThemeConstants::TableCellHeightPixels . "; background-color: " . ThemeConstants::TableCellNothingToOrderBGColor . ";";
}

/**
 * A class to hold Product order timing constants
 */
class ProductOrderTiming {
    /** Too late to order */
    const OnTime = "ontime";
    /** Late order */
    const KindaLate = "kindalate";
    /** Too late to order */
    const TooLate = "toolate";
}
?>