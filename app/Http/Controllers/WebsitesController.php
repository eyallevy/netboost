<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use Illuminate\Support\Facades\DB;
use App\Website;

class WebsitesController extends Controller
{
    private $urls = []; 
 
    public function get_data() {
        DB::table('website')->delete();

        // $urls[0][] = 'http://robinhoodbingo.com/'; 
        $urls[0][] = 'https://www.londonstockexchange.com/stock/XLM/xlmedia-plc/company-page?lang=en'; 

        $client = new Client();

        for ( $index = 0 ; $index <= 3 ; $index++) {
            if ( !empty($urls[$index]) ) {

                foreach($urls[$index] as &$value ) {
                    try {
                        set_time_limit (0);
                        $crawler = $client->request('GET',$value);
                        $response_code = $client->getResponse()->getStatusCode();

                        if ( $response_code == 200 ) {
                            $crawler->filter('a')->each(function ($link) use ( &$urls , $index ) {
                                $href = $link->extract(array('href'));
                                
                                if( !empty($href[0]) ) {
                                    $urls[$index+1][] = $href[0];
                                }
                            });

                            $urls[$index+1] = array_unique($urls[$index+1]);
                        } else {
                            print_r( "URL: {$value} ( StatusCode: {$response_code} ) <br>");
                        }
                    } catch (\Exception $ex)   {
                        error_log($ex);
                    }
                }
            }        
        }

        foreach( $urls as $websites) {
            foreach( $websites as $website) {
               $links[] = $website;       
            }
        }

        $links = array_unique($links);

        foreach( $links as $link) {
            $website = new Website();
            $website->url = $link;
            $website->save();
        }
        print_r( "Process was completed successfully");
    }
}
