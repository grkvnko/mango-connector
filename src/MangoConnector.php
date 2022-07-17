<?php

namespace grkvnko\MangoConnector;


class MangoAPIConnector
{
    private $client_credentials;

    /**
     * MangoAPIConnector constructor.
     * @param array $client_credentials
     * - @var string key
     * - @var string api_salt
     * - @var string widget_api
     * - @var string widget_api_key
     */
    public function __construct(array $client_credentials)
    {
        $this->client_credentials = $client_credentials;
    }

    private function sendRequest($action, $data)
    {
        $json = json_encode($data);
        $sign = hash('sha256', $this->client_credentials['key'] . $json . $this->client_credentials['api_salt']);
        $postdata = [
            'vpbx_api_key' => $this->client_credentials['key'],
            'sign' => $sign,
            'json' => $json
        ];
        $post = http_build_query($postdata);
        $ch = curl_init($this->client_credentials['uri_api'].$action);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    private function sendRequestWidget($action, $data)
    {
        $data['access_token'] = $this->client_credentials['widget_api_key'];

        $post = '';
        //$post = http_build_query($data);
        foreach ($data as $key => $value) {
            if ($post !== '') $post .= '&';
            $post .= $key . '=' . $value;
        }

        echo $post . PHP_EOL;
        echo $this->client_credentials['widget_api'] . $action .'?'. $post .PHP_EOL;

        $ch = curl_init($this->client_credentials['widget_api'] . $action .'?'. $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * подготовка статистики
     *
     * @param array $data
     * - @var integer date_from
     * - @var integer date_to
     * - @var string fields
     * - @var string request_id
     */
    public function stats_request($data) {
        return $this->sendRequest('stats/request', $data);
    }

    /**
     * Получение статистики
     *
     * @param array $data
     * - @var string key
     * - @var string request_id
     */
    public function stats_result($data) {
        return $this->sendRequest('stats/result', $data);
    }

    /**
     * Получение инфо и истории посещений по номеру телефона
     *
     * @param array $data
     * - @var string number
     */
    public function user_history_by_dct_number($data) {
        return $this->sendRequest('queries/user_history_by_dct_number', $data);
    }

    /**
     * Получение инфо и истории посещений по номеру телефона
     *
     * @param array $data
     * - @var string number
     */
    public function user_info_by_dct_number($data) {
        return $this->sendRequest('queries/user_info_by_dct_number', $data);
    }

    /**
     * получение списка сотрудников ВАТС
     *
     * @param array $data
     * - @var array ext_fields
     */
    public function config_users_request($data) {
        return $this->sendRequest('config/users/request', $data);
    }

    /**
     * получение списка учеток sip
     *
     * @param $data
     */
    public function sips($data) {
        return $this->sendRequest('sips', $data);
    }

    /**
     * получение списка звонков из виджета колтрекинга
     *
     * @param array $data
     * - @var string dateStart
     * - @var string dateEnd
     */
    public function getCalls($data) {
        return $this->sendRequestWidget('calls.json', $data);
    }
}