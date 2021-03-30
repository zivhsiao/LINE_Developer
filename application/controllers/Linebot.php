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
        $message = $event->{"message"};
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

        switch ($type) {
            case 'text':
                // $this->show_booking($parameter);
                $this->show_phone($replyToken, $message->{"text"}, $userId);
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
            case 'postback':
                
                $this->show_text($replyToken, '歡迎加入 Test');
                break;    
            default:
                $this->show_text($replyToken, '請輸入手機號碼進行註冊，謝謝！'); 
        }

    }

        
    /**
     * show_phone
     * 手機註冊
     *
     * @param  mixed $replyToken
     * @param  mixed $message
     * @return void
     */
    public function show_phone($replyToken, $message, $line_id){
        $userId = $line_id;

        if(is_numeric($message)) {
            if(strlen($message) == 10){
        
                $this->db->where('line_id', $userId);
                $chk_db = $this->db->get('line_accounts');
                $chk_dbs = $chk_db->result_array();

                $verify_code = 'c' . rand(10000, 99999);
        
                if(count($chk_dbs) > 0){
        
                    $data = array(
                        'account' => $message,
                        'status' => 0,
                        'verify_code' => $verify_code
                    );
                    $this->db->where('line_id', $userId);
                    $this->db->update('line_accounts', $data);

                    $this->show_text($replyToken, '歡迎進入遠悅飯店 LINE 機器人，可以瀏覽 FAQ 或相關的資訊');
        
                } else {
                    $data = array(
                        'line_id' => $userId,
                        'account' => $message,
                        'verify_code' => $verify_code
                    );
        
                    $this->db->insert('line_accounts', $data);

                    $this->show_text($replyToken, '歡迎加入遠悅飯店 LINE 機器人，請跟着我繼續完成每一步，以完成訂房');
                }

                $this->session->set_userdata('line_id', $userId);

                
            } else {
                $this->show_text($replyToken, '請輸入行動電話 10 個數字');
            }
        
        }
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
                        'originalContentUrl' => 'https://api.reh.tw/images/gonetone/logos/icons/icon-256x256.png', //回覆圖片
                        'previewImageUrl' => 'https://api.reh.tw/images/gonetone/logos/icons/icon-256x256.png' //回覆的預覽圖片
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