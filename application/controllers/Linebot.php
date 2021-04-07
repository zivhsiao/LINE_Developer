<?php

Class Linebot extends CI_Controller
{

    public function index()
    {
        //從Line Developer上申請的Token
        $accessToken = 'ZtwDijAi/mnWJXeSbxLeTqqGWUquvTRqxHHC1p5mUJVyDohBRjvkRheTo1B9ma8MIK4pUOUc+AaZmDeIZ3gHmvsZralbo3pDCzdIgbayDFYIpgl25XGsBHsg1A8ZImlTS/ty0Cl/3/M/r8PigOah5AdB04t89/1O/w1cDnyilFU=';

        //取得機器人丟過來的訊息
        $jsonString = file_get_contents('php://input');
        //轉成JSON
        $jsonObj = json_decode($jsonString);

        //設定變數給JSON的各欄位
        $event = $jsonObj->{"events"}[0];
        $type = $event->{"message"}->{"type"};
        $postback_type = $event->{"type"};
        $message = $event->{"message"};
        $text = $message->{'text'} ?? '';
        $replyToken = $event->{"replyToken"};

        $userId = $event->{"source"}->{"userId"};

        // 第一次使用
        $this->db->where('line_id', $userId);
        $chk = $this->db->get('line_accounts');
        $chks = $chk->result_array();

        if(count($chks) == 0){
            $dataInsert = array(
                "line_id" => $userId
            );

            $this->db->insert('line_accounts', $dataInsert);
        }

        if($postback_type == 'postback'){

            $params = json_encode($event->{"postback"});

            $this->show_text($replyToken, $params);
        } else {
            
            switch ($type) {
                case 'text':

                    // 輸入手機號碼
                    // $this->show_phone($replyToken, $text, $userId);
                    
                    // 顯示預約訂房
                    // if(strpos($text, '預約') !== false || strpos($text, '/r') !== false){
                    //     $this->show_room_location($replyToken, $text);
                    // }

                    // 顯示挑選日期
                    if(strpos($text, '日期') !== false){
                        $this->datetimepicker($replyToken, $message->{"text"});
                    }                                     
                    // 建構中
                    if(strpos($text, "QA客服") !== false || strpos($text, "/h") !== false){
                        $this->service($replyToken, $text);
                    }
                    if(strpos($text, "停車服務") !== false){
                        $this->car_park($replyToken, $text);
                    }
                    if(strpos($text, "觀光手冊") !== false){
                        $this->visit($replyToken, $text);
                    }                    

                    // 預約訂房
                    if(strpos($text, "預約訂房") !== false || strpos($text, "/r") !== false){
                        $this->show_check($replyToken, '請選擇要[住宿]或者[休息]');
                    }

                    // 我要休息
                    if(strpos($text, "我要休息") !== false){
                        $this->show_rest($replyToken);
                    }

                    // 我要住宿
                    if(strpos($text, "我要住宿") !== false){
                        $this->show_stay($replyToken);
                    }

                    // 房型介紹
                    if(strpos($text, "房型介紹") !== false){
                        $this->show_stay_room($replyToken, $text);
                    }

                    // 查看房型
                    if(strpos($text, "查看房間") !== false){
                        $this->show_check_room($replyToken, $text);
                    }

                    // 報到
                    if(strpos($text, "報到") !== false){
                        $this->show_checkin($replyToken, '報到');
                    }

                    if(strpos($text, "訂房資料") !== false){
                        $this->show_checkin($replyToken, '報到');
                    }

                    // // 訂房
                    if(strpos($text, "訂房") !== false){
                    //     // $this->show_phone($replyToken);
                    //     // $this->show_payment($replyToken, '訂房');
                        $this->show_phone($replyToken, $text,  $userId);
                        
                    }

                    if(is_numeric($text)) {
                        $this->show_payment($replyToken, '訂房');
                    }

                    // if(strpos($text, "訂房付款") !== false){
                    //     $this->show_payment($replyToken, '訂房');
                    // }
                    // $this->show_booking($parameter);
                    // $this->show_text($replyToken, $message->{"text"});
                    // $this->show_button_template($replyToken, $message->{"text"});
                    // $this->show_image($replyToken, $message->{"text"});
                    // $this->show_video($replyToken, $message->{"text"});
                    // $this->show_location($replyToken, $message->{"text"});
                    // $this->show_carousel($replyToken, $message->{"text"});
                    // $this->show_wininn($replyToken, $message->{"text"});                
    
                    // $this->show_room($replyToken, $message->{"text"});
    
                    // $this->show_other($replyToken, $message->{"text"});
                    
                    break; 
                default:
                    // if(empty($this->session->userdata('phone'))){
                       
                    // }
                     
            }
        }

        

    }

    
    /**
     * show_rest
     * 我要休息
     *
     * @param  mixed $replyToken
     * @return void
     */
    public function show_rest($replyToken){
        $postData = [
            'replyToken' => $replyToken,
            'messages' => [
                [
                    'type' => 'template', //訊息類型 (模板)
                    'altText' => '聯絡客服人員', //替代文字
                    'template' => [
                        'type' => 'buttons', //類型 (按鈕)
                        'thumbnailImageUrl' => 'https://life.tw/upload_file/50/content/93244597-5c98-4e76-f7f7-a3f0e6c14805.png', //圖片網址 <不一定需要>
                        'title' => '聯絡客服人員', //標題 <不一定需要>
                        'text' => '請點下面的連接', //文字
                        'actions' => [
                            
                            [
                                'type' => 'message', //類型 (訊息)
                                'label' => '聯絡客服人員', //標籤 2
                                'text' => 'https://page.line.me/wininn' //用戶發送文字
                            ]                   
                        ]
                    ]
                ]
            ]
        ];
        $this->show_message($postData);

    }

        
    /**
     * show_check
     * 當第一次登錄
     * @param  mixed $replyToken
     * @param  mixed $message
     * @return void
     */
    public function show_check($replyToken, $message){
        $postData = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'template', //訊息類型 (模板)
                    'altText' => $message, //替代文字
                    'template' => array(
                        'type' => 'buttons', //類型 (按鈕)
                        // 'thumbnailImageUrl' => 'https://api.reh.tw/line/bot/example/assets/images/example.jpg', //圖片網址 <不一定需要>
                        'title' => $message, //標題 <不一定需要>
                        'text' => '請選擇', //文字
                        'actions' => array(
                            
                            array(
                                'type' => 'message', //類型 (連結)
                                'label' => '住宿', //標籤 3
                                'text' => '我要住宿' //連結網址
                            ),
                            array(
                                'type' => 'message', //類型 (訊息)
                                'label' => '休息', //標籤 2
                                'text' => '我要休息' //用戶發送文字
                            ),
                        )
                    )
                )
            )
        );

        $this->show_message($postData);
    }
        
    /**
     * show_phone
     * 手機註冊
     *
     * @param  mixed $replyToken
     * @param  mixed $message
     * @return void
     */
    public function show_phone($replyToken, $message, $userId){
        // $userId = $line_id;

        // if(is_numeric($message)) {
        //     if(strlen($message) == 10){
        
                // $this->db->where('line_id', $userId);
                // $chk_db = $this->db->get('line_accounts');
                // $chk_dbs = $chk_db->result_array();

                // $verify_code = 'c' . rand(10000, 99999);
        
                // if(count($chk_dbs) > 0){
        
                //     $data = array(
                //         'account' => $message,
                //         'status' => 0,
                //         'verify_code' => $verify_code
                //     );
                //     $this->db->where('line_id', $userId);
                //     $this->db->update('line_accounts', $data);
        
                // } else {
                //     $data = array(
                //         'line_id' => $userId,
                //         'account' => $message,
                //         'verify_code' => $verify_code
                //     );
        
                //     $this->db->insert('line_accounts', $data);

                    
                // }

                // $this->session->set_userdata('line_id', $userId);

                $this->show_text($replyToken, '請輸入手機號碼');

                
            // } else {
            //     $this->show_text($replyToken, '請輸入行動電話 10 個數字');
            // }
        
        // }
    }
    
    /**
     * show_add_phone
     *
     * @param  mixed $replyToken
     * @return void
     */
    public function show_add_phone($replyToken){


        $postData = [
            "replyToken" => $replyToken,
            "messages" => [
                [
                    "type" => "text",
                    "text" => '訂房付款'
                ]
            ]
        ];

        $this->show_message($postData);
    }

        
    /**
     * show_text
     * 顯示字串，一般顯示
     * @param  mixed $replyToken
     * @param  mixed $message
     * @return void
     */
    public function show_text($replyToken, $message)
    {
        
        $postData = [
            "replyToken" => $replyToken,
            "messages" => [
                [
                    "type" => "text",
                    "text" => $message
                ]
            ]
        ];

        $this->show_message($postData);

        
    }
    
    /**
     * show_room_location
     * 地點
     * @param  mixed $replyToken
     * @param  mixed $message
     * @return void
     */
    public function show_stay($replyToken){
        
        $locations = [
            0 => [
                'name' => '新竹',
                'shop' => 1
            ],
            1 => [
                'name' => '嘉義',
                'shop' => 2 
            ], 
            2 => [
                'name' => '臺南',
                'shop' => 2
            ], 
            3 => [
                'name' => '高雄',
                'shop' => 1
            ], 
            4 => [
                'name' => '墾丁',
                'shop' => 2
            ]
        ];

        $postData = [
            'replyToken' => $replyToken,
            'messages' => [         
                [
                    "type" =>"template",
                    "altText" => '所有房型介紹',
                    "template" =>[
                        "type" =>"carousel",
                        "imageAspectRatio" =>"rectangle",
                        "imageSize" =>"cover",
                        "columns" =>[
                            [
                                "thumbnailImageUrl" =>"https://everydayobject.s3-ap-northeast-1.amazonaws.com/wp-content/uploads/2015/12/23112046/Teenager%E2%80%99s-Bedroom-1.jpg",
                                "imageBackgroundColor" =>"#a8e8fb",
                                "title" => $locations[0]['name'] . " 1 館",
                                "text" => $locations[0]['name'] . " 1 館" . " 簡短說明",
                                "defaultAction" =>[
                                    "type" =>"message",
                                    "label" =>"點到圖片或標題",
                                    "text" =>"0"
                                ],
                                "actions" =>[
                                    [
                                        "type" =>"message",
                                        "label" =>"查看房型",
                                        "text" => $locations[0]['name'] . "_1館_房型介紹"
                                    ],
                                    
                                ]
                            ],
                            [
                                "thumbnailImageUrl" =>"https://everydayobject.s3-ap-northeast-1.amazonaws.com/wp-content/uploads/2015/12/23112046/Teenager%E2%80%99s-Bedroom-1.jpg",
                                "imageBackgroundColor" =>"#a8e8fb",
                                "title" => $locations[1]['name'] . " 1 館",
                                "text" => $locations[1]['name'] . " 1 館" . " 簡短說明",
                                "defaultAction" =>[
                                    "type" =>"message",
                                    "label" =>"點到圖片或標題",
                                    "text" =>"0"
                                ],
                                "actions" =>[
                                    [
                                        "type" =>"message",
                                        "label" =>"查看房型",
                                        "text" => $locations[1]['name'] . "_1館_房型介紹"
                                    ],
                                    
                                ]
                            ],
                            [
                                "thumbnailImageUrl" =>"https://assets.everydayobject.us/wp-content/uploads/2020/10/11841133-1536x1152.jpg",
                                "imageBackgroundColor" =>"#a8e8fb",
                                "title" => $locations[1]['name'] . " 2 館",
                                "text" => $locations[1]['name'] . " 2 館" . " 簡短說明",
                                "defaultAction" =>[
                                    "type" =>"message",
                                    "label" =>"點到圖片或標題",
                                    "text" =>"0"
                                ],
                                "actions" =>[
                                    [
                                        "type" =>"message",
                                        "label" =>"查看房型",
                                        "text" => $locations[1]['name'] . "_2館_房型介紹"
                                    ],
                                    
                                ]
                            ],
                            [
                                "thumbnailImageUrl" =>"https://everydayobject.s3-ap-northeast-1.amazonaws.com/wp-content/uploads/2015/12/23112046/Teenager%E2%80%99s-Bedroom-1.jpg",
                                "imageBackgroundColor" =>"#a8e8fb",
                                "title" => $locations[2]['name'] . " 1 館",
                                "text" => $locations[2]['name'] . " 1 館" . " 簡短說明",
                                "defaultAction" =>[
                                    "type" =>"message",
                                    "label" =>"點到圖片或標題",
                                    "text" =>"0"
                                ],
                                "actions" =>[
                                    [
                                        "type" =>"message",
                                        "label" =>"查看房型",
                                        "text" => $locations[2]['name'] . "_1館_房型介紹"
                                    ],
                                    
                                ]
                            ],
                            [
                                "thumbnailImageUrl" =>"https://assets.everydayobject.us/wp-content/uploads/2020/10/11841133-1536x1152.jpg",
                                "imageBackgroundColor" =>"#a8e8fb",
                                "title" => $locations[2]['name'] . " 2 館",
                                "text" => $locations[2]['name'] . " 2 館" . " 簡短說明",
                                "defaultAction" =>[
                                    "type" =>"message",
                                    "label" =>"點到圖片或標題",
                                    "text" =>"0"
                                ],
                                "actions" =>[
                                    [
                                        "type" =>"message",
                                        "label" =>"查看房型",
                                        "text" => $locations[2]['name'] . "_2館_房型介紹"
                                    ],
                                    
                                ]
                            ],
                            [
                                "thumbnailImageUrl" =>"https://everydayobject.s3-ap-northeast-1.amazonaws.com/wp-content/uploads/2015/12/23112046/Teenager%E2%80%99s-Bedroom-1.jpg",
                                "imageBackgroundColor" =>"#a8e8fb",
                                "title" => $locations[3]['name'] . " 1 館",
                                "text" => $locations[3]['name'] . " 1 館" . " 簡短說明",
                                "defaultAction" =>[
                                    "type" =>"message",
                                    "label" =>"點到圖片或標題",
                                    "text" =>"0"
                                ],
                                "actions" =>[
                                    [
                                        "type" =>"message",
                                        "label" =>"查看房型",
                                        "text" => $locations[3]['name'] . "_1館_房型介紹"
                                    ],
                                    
                                ]
                            ],
                            [
                                "thumbnailImageUrl" =>"https://everydayobject.s3-ap-northeast-1.amazonaws.com/wp-content/uploads/2015/12/23112046/Teenager%E2%80%99s-Bedroom-1.jpg",
                                "imageBackgroundColor" =>"#a8e8fb",
                                "title" => $locations[4]['name'] . " 1 館",
                                "text" => $locations[4]['name'] . " 1 館" . " 簡短說明",
                                "defaultAction" =>[
                                    "type" =>"message",
                                    "label" =>"點到圖片或標題",
                                    "text" =>"0"
                                ],
                                "actions" =>[
                                    [
                                        "type" =>"message",
                                        "label" =>"查看房型",
                                        "text" => $locations[4]['name'] . "_1館_房型介紹"
                                    ],
                                    
                                ]
                            ],
                            [
                                "thumbnailImageUrl" =>"https://assets.everydayobject.us/wp-content/uploads/2020/10/11841133-1536x1152.jpg",
                                "imageBackgroundColor" =>"#a8e8fb",
                                "title" => $locations[4]['name'] . " 2 館",
                                "text" => $locations[4]['name'] . " 2 館" . " 簡短說明",
                                "defaultAction" =>[
                                    "type" =>"message",
                                    "label" =>"點到圖片或標題",
                                    "text" =>"0"
                                ],
                                "actions" =>[
                                    [
                                        "type" =>"message",
                                        "label" =>"查看房型",
                                        "text" => $locations[4]['name'] . "_2館_房型介紹"
                                    ],
                                    
                                ]
                            ],
                        ]
                    ]
                ]
            ]
        ];
        $this->show_message($postData);
    
    }
    
    /**
     * show_stay_room
     * 房型
     * 
     * @param  mixed $replyToken
     * @return void
     */
    public function show_stay_room($replyToken, $message){
        
        $msg_explode = explode('_', $message);
        
        $postData = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'template', //訊息類型 (模板)
                    'altText' => '房型介紹', //替代文字
                    'template' => array(
                        'type' => 'carousel', //類型 (輪播)
                        'columns' => array(
                            array(
                                'thumbnailImageUrl' => 'https://icrvb3jy.xinmedia.com/solomo/article/21658/AC78A3B3-77F4-40F1-8DEC-D9F1D61ED20F.jpg', //圖片網址 <不一定需要>
                                'title' => $msg_explode[0] . "_" . $msg_explode[1] . "_101", 
                                'text' => '房型說明', //文字 1
                                'actions' => array(
                                    array(
                                        'type' => 'message', //類型 (訊息)
                                        'label' => '查看房間', //標籤 2
                                        'text' => $msg_explode[0] . "_" . $msg_explode[1] . "_101_查看房間" //用戶發送文字
                                    ),
                                    array(
                                        'type' => 'message', //類型 (訊息)
                                        'label' => '訂房', //標籤 2
                                        'text' => $msg_explode[0] . "_" . $msg_explode[1] . "_101_訂房" //連結網址
                                    )
                                )
                            ),
                            array(
                                'thumbnailImageUrl' => 'https://cdn2.ettoday.net/images/616/d616907.jpg', //圖片網址 <不一定需要>
                                'title' => $msg_explode[0] . "_" . $msg_explode[1] . "_102", //標題 2 <不一定需要>
                                'text' => '房型說明', //文字 2
                                'actions' => array(                                   
                                    array(
                                        'type' => 'message', //類型 (訊息)
                                        'label' => '查看房間', //標籤 2
                                        'text' => $msg_explode[0] . "_" . $msg_explode[1] . "_102_查看房間" //用戶發送文字
                                    ),
                                    array(
                                        'type' => 'message', //類型 (訊息)
                                        'label' => '訂房', //標籤 2
                                        'text' => $msg_explode[0] . "_" . $msg_explode[1] . "_102_訂房" //連結網址
                                    )
                                )
                            )
                        )
                    )
                )
                
            )
        );

        $this->show_message($postData);
    }
    
    /**
     * show_check_room
     * 查看房間
     * 
     * @param  mixed $replyToken
     * @param  mixed $text
     * @return void
     */
    public function show_check_room($replyToken, $message ){
        $postData = [
            'replyToken' => $replyToken,
            'messages' => [
                [
                    'type' => 'template', //訊息類型 (模板)
                    'altText' => '查看房間', //替代文字
                    'template' => [
                        'type' => 'buttons', //類型 (按鈕)
                        'thumbnailImageUrl' => 'https://cp4.100.com.tw/images/articles/202009/30/admin_49_1601444020_IaYNyHSGy6.jpg!t1000.webp', //圖片網址 <不一定需要>
                        'title' => '查看房間', //標題 <不一定需要>
                        'text' => '查看房間', //文字
                        'actions' => [
                            [
                                'type' => 'message', //類型 (訊息)
                                'label' => '房間大門', //標籤 2
                                'text' => '房間大門' //用戶發送文字
                            ],
                            [
                                'type' => 'message', //類型 (訊息)
                                'label' => '床舖', //標籤 2
                                'text' => '床舖' //用戶發送文字
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $this->show_message($postData);
    }
    
    /**
     * show_payment
     * 付款
     * 
     * @param  mixed $replyToken
     * @param  mixed $message
     * @return void
     */
    public function show_payment($replyToken, $message){

        $postData = [
            'replyToken' => $replyToken,
            'messages' => [
                [
                    'type' => 'image', //訊息類型 (圖片)
                    'originalContentUrl' => 'https://photo.minwt.com/img/Content/webdesign/mwt-newebpay-sdk/mwt-newebpay-sdk_013.jpg', 
                    'previewImageUrl' => 'https://photo.minwt.com/img/Content/webdesign/mwt-newebpay-sdk/mwt-newebpay-sdk_013.jpg' 
                ]
            ]
        ];
        $this->show_message($postData);

    }
    
    /**
     * show_checkin
     * 報到
     * 
     * @return void
     */
    public function show_checkin($replyToken, $message){
        $postData = [
            "replyToken" => $replyToken,
            "messages" => [
                [
                    "type" => "text",
                    "text" => "您的訂房資料：\n訂單編號：A0000001\n名稱：王小明\n電話：0922123456\n房間號碼：嘉義 2館 101\n房門密碼：13579\n大門密碼：13579"
                ]
            ]
        ];

        $this->show_message($postData);
    }
    
    /**
     * datetimepicker
     *
     * @param  mixed $replyToken
     * @return void
     */
    public function datetimepicker($replyToken, $message){
      
        $postData = [
            'replyToken' => $replyToken,
            'messages' => [         
                [
                    "type" => "template",
                    "altText" => "this is a image carousel template",
                    "template" => [
                        "type" => "image_carousel",
                        "columns" => [
                            [
                                "imageUrl" => "https://pix6.agoda.net/hotelImages/625/6257553/6257553_18120717320070156885.jpg?s=1024x768",
                                "action" => [
                                "type" => "datetimepicker",
                                "label" => "選日期時間",
                                "data" => "roomId",
                                "mode" => "date"
                                ]
                            ]
                        ]
                    ]
                    ]
            ]
        ];
        $this->show_message($postData);    
    }

    
    /**
     * service
     * QA 客服
     * @param  mixed $replyToken
     * @param  mixed $message
     * @return void
     */
    public function service($replyToken, $message){
        
        $postData = [
            "replyToken" => $replyToken,
            "messages" => [
                [
                    "type" => "text",
                    "text" => "QA 客服目前建構中，稍後將有客服人員與您聯繫，謝謝！"
                ]
            ]
        ];

        $this->show_message($postData);
    
    }
    
    /**
     * car_park
     * 停車服務
     * @param  mixed $replyToken
     * @param  mixed $message
     * @return void
     */
    public function car_park($replyToken, $message){

        $postData = [
            "replyToken" => $replyToken,
            "messages" => [
                [
                    "type" => "text",
                    "text" => "停車服務目前建構中，稍後將有客服人員與您聯繫，謝謝！"
                ]
            ]
        ];

        $this->show_message($postData);

    }
    
    /**
     * visit
     * 觀光手冊
     * @param  mixed $replyToken
     * @param  mixed $message
     * @return void
     */
    public function visit($replyToken, $message){
        
        $postData = [
            "replyToken" => $replyToken,
            "messages" => [
                [
                    "type" => "text",
                    "text" => "觀光手冊目前建構中，稍後將有客服人員與您聯繫，謝謝！"
                ]
            ]
        ];

        $this->show_message($postData);
    
    }

    public function show_message($postData) {

        $accessToken = 'ZtwDijAi/mnWJXeSbxLeTqqGWUquvTRqxHHC1p5mUJVyDohBRjvkRheTo1B9ma8MIK4pUOUc+AaZmDeIZ3gHmvsZralbo3pDCzdIgbayDFYIpgl25XGsBHsg1A8ZImlTS/ty0Cl/3/M/r8PigOah5AdB04t89/1O/w1cDnyilFU=';

        //post url init
        $ch = curl_init("https://api.line.me/v2/bot/message/reply");

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
            //'Authorization: Bearer '. TOKEN
        ));

        curl_exec($ch);
        curl_close($ch);
    }


    public function show_booking($replyToken, $message){
        if(strpos($message, "訂房") !== false){
            $postData = [
                "replyToken" => $replyToken,
                "messages" => [
                    [
                        "type" => "text",
                        "text" => $message
                    ]
                ]
            ];
    
            $this->show_message($postData);
        }
        
    }

    public function show_wininn($replyToken, $message){
        if (strpos($message, "報到") !== false) {
            $postData = [
                'replyToken' => $replyToken,
                'messages' => [
                    [
                        'type' => 'template', //訊息類型 (模板)
                        'altText' => '報到 Demo', //替代文字
                        'template' => [
                            'type' => 'buttons', //類型 (按鈕)
                            'thumbnailImageUrl' => 'https://api.reh.tw/line/bot/example/assets/images/example.jpg', //圖片網址 <不一定需要>
                            'title' => 'Example Menu', //標題 <不一定需要>
                            'text' => 'Please select', //文字
                            'actions' => [
                                [
                                    'type' => 'postback', //類型 (回傳)
                                    'label' => 'Postback example', //標籤 1
                                    'data' => 'action=buy&itemid=123' //資料
                                ],
                                [
                                    'type' => 'message', //類型 (訊息)
                                    'label' => 'Message example', //標籤 2
                                    'text' => 'Message example' //用戶發送文字
                                ],
                                [
                                    'type' => 'uri', //類型 (連結)
                                    'label' => 'Uri example', //標籤 3
                                    'uri' => 'https://github.com/GoneToneStudio/line-example-bot-tiny-php' //連結網址
                                ]                   
                            ]
                        ]
                    ]
                ]
            ];

            $this->show_message($postData);
        }
    }

    public function show_room($replyToken, $message){
        if (strpos($message, "房型") !== false) {
            $postData = array(
                'replyToken' => $replyToken,
                'messages' => array(
                    array(
                        'type' => 'template', //訊息類型 (模板)
                        'altText' => '房型介紹', //替代文字
                        'template' => array(
                            'type' => 'carousel', //類型 (輪播)
                            'columns' => array(
                                array(
                                    'thumbnailImageUrl' => 'https://icrvb3jy.xinmedia.com/solomo/article/21658/AC78A3B3-77F4-40F1-8DEC-D9F1D61ED20F.jpg', //圖片網址 <不一定需要>
                                    'title' => '房型 1', //標題 1 <不一定需要>
                                    'text' => '房型說明 1', //文字 1
                                    'actions' => array(
                                        array(
                                            'type' => 'postback', //類型 (回傳)
                                            'label' => 'Postback example 1', //標籤 1
                                            'data' => 'action=buy&itemid=123' //資料
                                        ),
                                        array(
                                            'type' => 'message', //類型 (訊息)
                                            'label' => 'Message example 1', //標籤 2
                                            'text' => 'Message example 1' //用戶發送文字
                                        ),
                                        array(
                                            'type' => 'uri', //類型 (連結)
                                            'label' => 'Uri example 1', //標籤 3
                                            'uri' => 'https://github.com/GoneToneStudio/line-example-bot-tiny-php' //連結網址
                                        )
                                    )
                                ),
                                array(
                                    'thumbnailImageUrl' => 'https://cdn2.ettoday.net/images/616/d616907.jpg', //圖片網址 <不一定需要>
                                    'title' => '房型 2', //標題 2 <不一定需要>
                                    'text' => '房型說明 2', //文字 2
                                    'actions' => array(
                                        array(
                                            'type' => 'postback', //類型 (回傳)
                                            'label' => 'Postback example 2', //標籤 1
                                            'data' => 'action=buy&itemid=123' //資料
                                        ),
                                        array(
                                            'type' => 'message', //類型 (訊息)
                                            'label' => 'Message example 2', //標籤 2
                                            'text' => 'Message example 2' //用戶發送文字
                                        ),
                                        array(
                                            'type' => 'uri', //類型 (連結)
                                            'label' => 'Uri example 2', //標籤 3
                                            'uri' => 'https://github.com/GoneToneStudio/line-example-bot-tiny-php' //連結網址
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            );

            $this->show_message($postData);
        }
    }
    
    public function show_carousel($replyToken, $message){
        if ($message == "輪播模板") {
            $postData = array(
                'replyToken' => $replyToken,
                'messages' => array(
                    array(
                        'type' => 'template', //訊息類型 (模板)
                        'altText' => 'Example buttons template', //替代文字
                        'template' => array(
                            'type' => 'carousel', //類型 (輪播)
                            'columns' => array(
                                array(
                                    'thumbnailImageUrl' => 'https://icrvb3jy.xinmedia.com/solomo/article/21658/AC78A3B3-77F4-40F1-8DEC-D9F1D61ED20F.jpg', //圖片網址 <不一定需要>
                                    'title' => '房型 1', //標題 1 <不一定需要>
                                    'text' => '房型說明 1', //文字 1
                                    'actions' => array(
                                        array(
                                            'type' => 'postback', //類型 (回傳)
                                            'label' => 'Postback example 1', //標籤 1
                                            'data' => 'action=buy&itemid=123' //資料
                                        ),
                                        array(
                                            'type' => 'message', //類型 (訊息)
                                            'label' => 'Message example 1', //標籤 2
                                            'text' => 'Message example 1' //用戶發送文字
                                        ),
                                        array(
                                            'type' => 'uri', //類型 (連結)
                                            'label' => 'Uri example 1', //標籤 3
                                            'uri' => 'https://github.com/GoneToneStudio/line-example-bot-tiny-php' //連結網址
                                        )
                                    )
                                ),
                                array(
                                    'thumbnailImageUrl' => 'https://cdn2.ettoday.net/images/616/d616907.jpg', //圖片網址 <不一定需要>
                                    'title' => '房型 2', //標題 2 <不一定需要>
                                    'text' => '房型說明 2', //文字 2
                                    'actions' => array(
                                        array(
                                            'type' => 'postback', //類型 (回傳)
                                            'label' => 'Postback example 2', //標籤 1
                                            'data' => 'action=buy&itemid=123' //資料
                                        ),
                                        array(
                                            'type' => 'message', //類型 (訊息)
                                            'label' => 'Message example 2', //標籤 2
                                            'text' => 'Message example 2' //用戶發送文字
                                        ),
                                        array(
                                            'type' => 'uri', //類型 (連結)
                                            'label' => 'Uri example 2', //標籤 3
                                            'uri' => 'https://github.com/GoneToneStudio/line-example-bot-tiny-php' //連結網址
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            );

            $this->show_message($postData);
        }
    }

    public function show_location($replyToken, $message){

        if ($message == "位置") {
            $postData = [
                'replyToken' => $replyToken,
                'messages' => [
                    [
                        'type' => 'location', //訊息類型 (位置)
                        'title' => 'Example location', //回覆標題
                        'address' => '台灣高雄市三民區大昌一路 98 號 (立志中學)', //回覆地址
                        'latitude' => 22.653742, //地址緯度
                        'longitude' => 120.32652400000006 //地址經度
                    ]
                ]
            ];

            $this->show_message($postData);

        }
    }

    public function show_video($replyToken, $message){
        if ($message == "影片") {
            $postData = [
                'replyToken' => $replyToken,
                'messages' => [
                    [
                        'type' => 'video', //訊息類型 (影片)
                        'originalContentUrl' => 'https://api.reh.tw/line/bot/example/assets/videos/example.mp4', //回覆影片
                        'previewImageUrl' => 'https://api.reh.tw/line/bot/example/assets/images/example.jpg' //回覆的預覽圖片
                    ]
                ]
            ];

            $this->show_message($postData);
        }
    }

    public function show_image($replyToken, $message) {
        if ($message == "圖片") {
            $postData = [
                'replyToken' => $replyToken,
                'messages' => [
                    [
                        'type' => 'image', //訊息類型 (圖片)
                        'originalContentUrl' => 'https://api.reh.tw/images/gonetone/logos/icons/icon-256x256.png', 
                        'previewImageUrl' => 'https://api.reh.tw/images/gonetone/logos/icons/icon-256x256.png' 
                    ]
                ]
            ];
            $this->show_message($postData);
        }
    }

    public function show_other($replyToken, $message){
        $postData = [
            "replyToken" => $replyToken,
            "messages" => [
                [
                    "type" => "text",
                    "text" => $message
                ]
            ]
        ];

        $this->show_message($postData);
    }

    public function show_button_template($replyToken, $message){
        if ($message == "按鈕模板") {
            $postData = [
                'replyToken' => $replyToken,
                'messages' => [
                    [
                        'type' => 'template', //訊息類型 (模板)
                        'altText' => 'Example buttons template', //替代文字
                        'template' => [
                            'type' => 'buttons', //類型 (按鈕)
                            'thumbnailImageUrl' => 'https://api.reh.tw/line/bot/example/assets/images/example.jpg', //圖片網址 <不一定需要>
                            'title' => 'Example Menu', //標題 <不一定需要>
                            'text' => 'Please select', //文字
                            'actions' => [
                                [
                                    'type' => 'postback', //類型 (回傳)
                                    'label' => 'Postback example', //標籤 1
                                    'data' => 'action=buy&itemid=123' //資料
                                ],
                                [
                                    'type' => 'message', //類型 (訊息)
                                    'label' => 'Message example', //標籤 2
                                    'text' => 'Message example' //用戶發送文字
                                ],
                                [
                                    'type' => 'uri', //類型 (連結)
                                    'label' => 'Uri example', //標籤 3
                                    'uri' => 'https://github.com/GoneToneStudio/line-example-bot-tiny-php' //連結網址
                                ]                   
                            ]
                        ]
                    ]
                ]
            ];

            $this->show_message($postData);
        }
    }
}