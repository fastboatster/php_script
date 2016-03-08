<?php
function quote_error() {
echo ("<Quote>Error</Quote>");
};
function news_error () {
echo ("<News>Error</News>");
};
function quote_output ($stock_quote) {

$change=(string)$stock_quote ->results -> quote -> Change;

if ($change=="") {
	quote_error();
	}
else {
	echo ("<Quote>");

	$bid=(double)$stock_quote ->results -> quote -> Bid;
	
	$change_perc=(double)$stock_quote ->results -> quote -> ChangeinPercent;
	
	$last_trade_price= (double)$stock_quote -> results -> quote -> LastTradePriceOnly;
	
	$prev_close=(double)$stock_quote ->results -> quote -> PreviousClose;	
	
	$days_low = (double)$stock_quote ->results -> quote ->DaysLow;		
	
	$days_high = (double)$stock_quote ->results -> quote ->DaysHigh;
	
	$open= (double)$stock_quote ->results -> quote -> Open;
	
	$year_low=(double)$stock_quote ->results -> quote -> YearLow;
	
	$year_high=(double)$stock_quote ->results -> quote -> YearHigh;
	
	//output $bid here
	
	$volume=(double)$stock_quote ->results -> quote ->Volume;
	
	$ask=(double)$stock_quote ->results -> quote -> Ask;
	
	$avg_vol=(double)$stock_quote ->results -> quote -> AverageDailyVolume;	

	$one_yr_target_price=(double)$stock_quote ->results -> quote -> OneyrTargetPrice;
	
	$market_cap= (string)$stock_quote ->results -> quote -> MarketCapitalization;
	
	//formatted numbers here below:
				
	$last_trade_price_f = number_format($last_trade_price,2);
	
			$bid_f = number_format($bid, 2);
			$prev_close_f = number_format($prev_close, 2);
			$ask_f = number_format($ask, 2);
			$open_f = number_format($open, 2);
			$year_low_f = number_format($year_low, 2);
			$year_high_f = number_format($year_high, 2);
			$avg_vol_f = number_format($avg_vol);
			$one_yr_target_price_f= number_format($one_yr_target_price, 2);
			$days_low_f = number_format($days_low, 2);
			$days_high_f= number_format($days_high, 2);
			$volume_f= number_format($volume);
			$change_f = abs($change);
			$change_perc_abs = abs($change_perc);
			$change_perc_f = number_format($change_perc_abs, 2);
			if ($change>0) {
			$change_type="<ChangeType>+</ChangeType>";
			}
			else {
			$change_type="<ChangeType>-</ChangeType>";
			}
			$formatted_quote = $change_type . "<Change>" . $change_f . "</Change><ChangeInPercent>" . $change_perc_f . "%</ChangeInPercent><LastTradePriceOnly>" . $last_trade_price_f . "</LastTradePriceOnly><PreviousClose>" . $prev_close_f . "</PreviousClose><DaysLow>" . $days_low_f . "</DaysLow><DaysHigh>" . $days_high_f . "</DaysHigh><Open>" . $open_f . "</Open><YearLow>" . $year_low_f . "</YearLow><YearHigh>" . $year_high_f . "</YearHigh><Bid>" . $bid_f . "</Bid><Volume>" . $volume_f . "</Volume><Ask>" . $ask_f . "</Ask><AverageDailyVolume>" . $avg_vol_f . "</AverageDailyVolume><OneYearTargetPrice>" . $one_yr_target_price_f . "</OneYearTargetPrice><MarketCapitalization>" . $market_cap . "</MarketCapitalization>";
			echo $formatted_quote;
			echo ("</Quote>");
};

}

function news_output ($news) {

$first_title=(string)$news->channel->item[0]->title;
		
if ($first_title == 'Yahoo! Finance: RSS feed not found') {

	news_error();
	
	}
else {

	echo "<News>";
	
	foreach ($news->channel->item as $news_item) {
			$title= (string)$news_item -> title;
			$title_f = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
			$url_value= (string)$news_item -> link;
			$url_f = htmlspecialchars($url_value, ENT_QUOTES, 'UTF-8');
			$news_out = "<Item><Title>" . $title_f . "</Title><Link>" . $url_f . "</Link></Item>";
			
			echo $news_out;
			}
	echo "</News>";		
			
}	


};

if (isset($_GET['symbol'])) :?>

<?php header ('Content-type: text/xml; charset=utf-8');
?>

<result>
<?php

libxml_use_internal_errors(true);

$name_stock = urlencode($_GET["symbol"]);

$stock_request = 'http://query.yahooapis.com/v1/public/yql?q=Select%20Name%2C%20Symbol%2C%20LastTradePriceOnly%2C%20Change%2C%20ChangeinPercent%2C%20PreviousClose%2C%20DaysLow%2C%20DaysHigh%2C%20Open%2C%20YearLow%2C%20YearHigh%2C%20Bid%2C%20Ask%2C%20AverageDailyVolume%2C%20OneyrTargetPrice%2C%20MarketCapitalization%2C%20Volume%2C%20Open%2C%20YearLow%20from%20yahoo.finance.quotes%20where%20symbol%3D%22' . $name_stock . '%22&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';

$stock_quote = @simplexml_load_file($stock_request);

	if ($stock_quote === false) {
		quote_error();
		libxml_clear_errors();
	}
	else {
	
	$company_name=(string)$stock_quote -> results -> quote -> Name;
	
	$company_symbol=(string)$stock_quote -> results -> quote -> Symbol;
	
	$company_symbol_f = htmlspecialchars($company_symbol, ENT_QUOTES, 'UTF-8');
	
	$company_name_f = htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8');
	
	$name_symbol = "<Name>" . $company_name_f . "</Name><Symbol>" . $company_symbol_f . "</Symbol>";
	
	echo $name_symbol;
	
	quote_output ($stock_quote);
	}

$news_request = 'http://feeds.finance.yahoo.com/rss/2.0/headline?s='. $name_stock . '&region=US&lang=en-US';

$news = @simplexml_load_file($news_request);

	if ($news ===false) {
	
		news_error();
		
		libxml_clear_errors();
		
	}
	else { 
	
	
		$simple_xml_array = $news->getName();
		
		$array_count = $news->children();
	
		if (empty($simple_xml_array) && count($array_count) == 0) { 
		
			news_error();
			
		}
		
		else {
	
			news_output($news);
	
		};
	};

$stock_chart_url = 'http://chart.finance.yahoo.com/t?s=' . $name_stock . '&amp;lang=en-US&amp;amp;width=300&amp;height=180';

$stock_chart_url_f = htmlspecialchars($stock_chart_url, ENT_QUOTES);

$chart_url_node = "<StockChartImageURL>" . $stock_chart_url_f . "</StockChartImageURL>";

echo $chart_url_node;

endif
?>
</result>