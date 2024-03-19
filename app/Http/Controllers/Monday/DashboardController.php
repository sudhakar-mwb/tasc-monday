<?php

namespace App\Http\Controllers\Monday;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Traits\MondayApis;


class DashboardController extends Controller
{
    use  MondayApis;

    public function dashboard (){
        $heading = 'Onboardify Command Center'; 
        $cards = [
            [
                'title' => 'Request Onboarding ',
                'btn_text' => 'Request',
                'icon' =>
                    '<span class="iconElement__inner_2iN"><svg xmlns="http://www.w3.org/2000/svg" data-name="Слой 1" viewBox="0 0 128 128" x="0px" y="0px" style="max-width:100%" height="100%"><title style="" fill="currentColor">ic_request_group</title><path d="M90.16,92.23a16.15,16.15,0,1,0-17.45,0A27.35,27.35,0,0,0,64,97,38.59,38.59,0,0,0,49,88.83a22.26,22.26,0,1,0-20.75,0A38.66,38.66,0,0,0,0,126a2,2,0,0,0,4,0,34.6,34.6,0,0,1,69.19,0,2,2,0,0,0,4,0A38.46,38.46,0,0,0,66.92,99.82,23.56,23.56,0,0,1,105,118.37a2,2,0,0,0,4,0A27.61,27.61,0,0,0,90.16,92.23ZM20.34,69.15A18.26,18.26,0,1,1,38.59,87.41,18.28,18.28,0,0,1,20.34,69.15Zm49,9.51A12.15,12.15,0,1,1,81.44,90.81,12.16,12.16,0,0,1,69.29,78.66Z" style="" fill="currentColor"></path><path d="M128,44.66c-.18-2.75-2.34-3.79-3.92-4.55l-.82-.41h0a8,8,0,0,1-4.12-6.65V20.41a20.63,20.63,0,0,0-41.26,0V33.05a8,8,0,0,1-4.12,6.65c-.24.13-.49.25-.74.36-1.52.74-3.82,1.84-4,4.6A5.92,5.92,0,0,0,70.7,49,6,6,0,0,0,75,51H88.18a10.42,10.42,0,0,0,20.64,0H122a6,6,0,0,0,4.31-2A5.92,5.92,0,0,0,128,44.66ZM98.5,56a6.43,6.43,0,0,1-6.26-5h12.52A6.43,6.43,0,0,1,98.5,56Zm24.89-9.73A2.16,2.16,0,0,1,122,47H75a2.16,2.16,0,0,1-1.38-.75A2,2,0,0,1,73,44.92c0-.37.46-.63,1.74-1.25.29-.14.57-.27.85-.42a12,12,0,0,0,6.28-10.2V20.41a16.63,16.63,0,0,1,33.26,0V33.05a12,12,0,0,0,6.28,10.2l.92.46c1.51.73,1.65.89,1.67,1.21A2,2,0,0,1,123.39,46.27Z" style="" fill="currentColor"></path></svg></span>',
                'description' =>
                    'Streamline your employee onboarding with TASC Outsourcing. Request here for a hassle-free experience, letting us handle the rest with care and efficiency.',
           'link'=> URL::to('/').'/monday/form/track-request'
                ],
            [
                'title' => 'Track Onboarding',
                'btn_text' => 'Track',
                'icon' =>
                    '<span class="iconElement__inner_2iN"><svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" version="1.1" x="0px" y="0px" viewBox="0 0 100 100" style="max-width:100%" height="100%"><g transform="translate(0,-952.36218)" style="" fill="currentColor"><path style="text-indent:0;text-transform:none;direction:ltr;block-progression:tb;baseline-shift:baseline;color:#000000;enable-background:accumulate" d="m 37.012126,960.36576 c -16.0043,0 -29,12.99558 -29,29.00001 0,16.00443 12.9957,29.00003 29,29.00003 7.6449,0 14.5979,-2.9705 19.7813,-7.8125 l 4.5313,4.5312 -2.125,2.125 c -0.3655,0.3697 -0.3655,1.0366 0,1.4063 l 25.437398,25.4687 c 0.3697,0.3655 1.0366,0.3655 1.4063,0 l 5.6563,-5.6562 c 0.3842,-0.3718 0.3842,-1.0657 0,-1.4375 l -25.437598,-25.4375 c -0.3697,-0.3655 -1.0365,-0.3655 -1.4062,0 l -2.125,2.125 -4.5,-4.5313 c 4.8333,-5.1817 7.7812,-12.14413 7.7812,-19.78123 0,-16.00443 -12.9956,-29.00001 -29,-29.00001 z m 0,2 c 14.9235,0 27,12.07646 27,27.00001 0,14.92353 -12.0765,27.00003 -27,27.00003 -14.9236,0 -27,-12.0765 -27,-27.00003 0,-14.92355 12.0764,-27.00001 27,-27.00001 z m 0,5 c -5.3158,0 -10.6381,2.08089 -14.6874,6.25 -0.4228,0.59717 -0.4657,1.05369 -0.026,1.47395 0.6635,0.40454 0.9533,0.31438 1.4325,-0.0365 7.3454,-7.56266 19.217,-7.56269 26.5625,0 0.3127,0.34071 0.8672,0.41003 1.25,0.15625 0.5062,-0.32717 0.5888,-1.169 0.1563,-1.59375 -4.0494,-4.16911 -9.3717,-6.25 -14.6876,-6.25 z m 28.5313,46.31254 24.031298,24.0312 -4.2188,4.25 -24.062498,-24.0625 z" fill="currentColor" fill-opacity="1" stroke="currentColor" marker="none" visibility="visible" display="inline" overflow="visible"></path></g></svg></span>',
                'description' =>
                    'Track your onboarding requests seamlessly with us. Stay updated on the progress of your employee onboarding journey. Effortless tracking for a smoother onboarding experience.',
                    'link'=> URL::to('/').'/monday/form/track-request'
                ],
            [
                'title' => 'Overall Status',
                'btn_text' => 'Check',
                'icon' =>
                    '<span class="iconElement__inner_2iN"><svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" version="1.1" x="0px" y="0px" viewBox="0 0 100 100" style="max-width:100%" height="100%"><g transform="translate(0,-952.36218)" style="" fill="currentColor"><path style="font-size:medium;font-style:normal;font-variant:normal;font-weight:normal;font-stretch:normal;text-indent:0;text-align:start;text-decoration:none;line-height:normal;letter-spacing:normal;word-spacing:normal;text-transform:none;direction:ltr;block-progression:tb;writing-mode:lr-tb;text-anchor:start;baseline-shift:baseline;opacity:1;color:#000000;fill-opacity:1;stroke:none;stroke-width:4;marker:none;visibility:visible;display:inline;overflow:visible;enable-background:accumulate;font-family:Sans;-inkscape-font-specification:Sans" d="M 40.8125 13 A 2.0002 2.0002 0 0 0 39 15 L 39 78 A 2.0002 2.0002 0 0 0 41 80 L 59 80 A 2.0002 2.0002 0 0 0 61 78 L 61 15 A 2.0002 2.0002 0 0 0 59 13 L 41 13 A 2.0002 2.0002 0 0 0 40.8125 13 z M 43 17 L 57 17 L 57 76 L 43 76 L 43 17 z M 67.8125 36 A 2.0002 2.0002 0 0 0 66 38 L 66 78 A 2.0002 2.0002 0 0 0 68 80 L 86 80 A 2.0002 2.0002 0 0 0 88 78 L 88 38 A 2.0002 2.0002 0 0 0 86 36 L 68 36 A 2.0002 2.0002 0 0 0 67.8125 36 z M 70 40 L 84 40 L 84 76 L 70 76 L 70 40 z M 13.8125 50 A 2.0002 2.0002 0 0 0 12 52 L 12 78 A 2.0002 2.0002 0 0 0 14 80 L 32 80 A 2.0002 2.0002 0 0 0 34 78 L 34 52 A 2.0002 2.0002 0 0 0 32 50 L 14 50 A 2.0002 2.0002 0 0 0 13.8125 50 z M 16 54 L 30 54 L 30 76 L 16 76 L 16 54 z M 7.8125 83 A 2.0021961 2.0021961 0 1 0 8 87 L 92 87 A 2.0002 2.0002 0 1 0 92 83 L 8 83 A 2.0002 2.0002 0 0 0 7.8125 83 z " transform="translate(0,952.36218)" fill="currentColor"></path></g></svg></span>',
                'description' =>
                    'Stay in the loop with ease! Check the overall status of your onboarding requests and keep tabs on your employee onboarding progress for a comprehensive overview of the entire process.',
                    'link'=> URL::to('/').'/monday/form/track-request'
                ],
            // Add more card data as needed
        ];
        return view('admin.dashboard',compact('heading','cards'));
    }
    public function trackRequest (Request $request){
        $query = "query {
            boards(ids: 1352607400) {
               columns {
                  title
                  id
               }
               items_page (limit: 10, cursor:null) {
                  cursor
                  items {
                      id
                      name
                      email
                      column_values {
                         id
                         value
                         type
                         ... on StatusValue  {
                            label
                            update_id
                         }
                     }
                  }
              }
            }
         }";
        $response = $this->_get($query)['response'];
        $heading = 'Request Tracking'; 
        $subheading= 'Track your onboarding progress effortlessly by using our request-tracking center';
        
        return view('admin.track_request',compact('heading','subheading','response'));
    }
}
