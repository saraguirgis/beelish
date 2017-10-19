<?php
class BusinessConfigs {
	const LatePenaltyChargeInDollars = 1.00;
	
	const ChangesDeadlineInBusinessDays = 2;

	const ResetProductState = false;

	const DefaultProductStockQuantity = 200;

	const ChangeWindowStockQuantity = 50;

	const Holidays = array(
		"11/23/2017",
		"11/24/2017",
		"12/25/2017",
		"12/24/2017",
		"01/01/2017");
	
    const OctoberProducts = array(
	//	"10/02/2017" => ProductCalendar::NoLunchProductId,
	//	"10/03/2017" => ProductCalendar::NoLunchProductId,
	//	"10/04/2017" => ProductCalendar::NoLunchProductId,
	//	"10/05/2017" => ProductCalendar::NoLunchProductId,
	//	"10/06/2017" => ProductCalendar::NoLunchProductId,

	//	"10/09/2017" => ProductCalendar::NoLunchProductId,
	//	"10/10/2017" => ProductCalendar::NoLunchProductId,
	//	"10/11/2017" => ProductCalendar::NoLunchProductId,
	//	"10/12/2017" => ProductCalendar::NoLunchProductId,
	//	"10/13/2017" => ProductCalendar::NoLunchProductId,

	//	"10/16/2017" => ProductCalendar::NoLunchProductId,
	//	"10/17/2017" => ProductCalendar::NoLunchProductId,
	//	"10/18/2017" => ProductCalendar::NoLunchProductId,
	//	"10/19/2017" => ProductCalendar::NoLunchProductId,
	//	"10/20/2017" => ProductCalendar::NoLunchProductId,

		"10/23/2017" => ProductCalendar::NoLunchProductId,
		"10/24/2017" => ProductCalendar::NoLunchProductId,
		"10/25/2017" => ProductCalendar::NoLunchProductId,
		"10/26/2017" => ProductCalendar::NoLunchProductId,
		"10/27/2017" => ProductCalendar::NoLunchProductId,

		"10/30/2017" => 973,
		"10/31/2017" => 974,
		"11/01/2017" => ProductCalendar::NoDetailsProductId,
		"11/02/2017" => ProductCalendar::NoDetailsProductId,
        "11/03/2017" => ProductCalendar::NoDetailsProductId,
    );
    
    const NovemberProducts = array(
		"10/30/2017" => ProductCalendar::NoDetailsProductId,
		"10/31/2017" => ProductCalendar::NoDetailsProductId,
		"11/01/2017" => 975,
		"11/02/2017" => 976,
		"11/03/2017" => 977,

		"11/06/2017" => 978,
		"11/07/2017" => 979,
		"11/08/2017" => 980,
		"11/09/2017" => 990,
		"11/10/2017" => ProductCalendar::NoLunchProductId,

		"11/14/2017" => 991,
		"11/13/2017" => 992,
		"11/15/2017" => 993,
		"11/16/2017" => 1004,
		"11/17/2017" => 1005,

		"11/20/2017" => 1015,
		"11/21/2017" => 1016,
		"11/22/2017" => ProductCalendar::NoLunchProductId,
		"11/23/2017" => ProductCalendar::NoLunchProductId,
		"11/24/2017" => ProductCalendar::NoLunchProductId,

		"11/27/2017" => 1017,
		"11/28/2017" => 1018,
		"11/29/2017" => 1029,
		"11/30/2017" => 1030,
        "12/01/2017" => ProductCalendar::NoDetailsProductId,
    );

    const DecemberProducts = array(
		"11/27/2017" => ProductCalendar::NoDetailsProductId,
		"11/28/2017" => ProductCalendar::NoDetailsProductId,
		"11/29/2017" => ProductCalendar::NoDetailsProductId,
		"11/30/2017" => ProductCalendar::NoDetailsProductId,
		"12/01/2017" => 1031,

		"12/04/2017" => 1042,
		"12/05/2017" => 1043,
		"12/06/2017" => 1044,
		"12/07/2017" => 1053,
		"12/08/2017" => 1054,

		"12/11/2017" => 1055,
		"12/12/2017" => 1066,
		"12/13/2017" => 1067,
		"12/14/2017" => 1068,
		"12/15/2017" => 1078,

		"12/18/2017" => ProductCalendar::NoLunchProductId,
		"12/19/2017" => ProductCalendar::NoLunchProductId,
		"12/20/2017" => ProductCalendar::NoLunchProductId,
		"12/21/2017" => ProductCalendar::NoLunchProductId,
		"12/22/2017" => ProductCalendar::NoLunchProductId,
     
		"12/25/2017" => ProductCalendar::NoLunchProductId,
		"12/26/2017" => ProductCalendar::NoLunchProductId,
		"12/27/2017" => ProductCalendar::NoLunchProductId,
		"12/28/2017" => ProductCalendar::NoLunchProductId,
        "12/29/2017" => ProductCalendar::NoLunchProductId,
    );
}
?>