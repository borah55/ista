<?php
// Supabase configuration
define('SUPABASE_URL', getenv('SUPABASE_URL'));
define('SUPABASE_KEY', getenv('SUPABASE_KEY'));

// Telegram Bot Token
define('BOT_TOKEN', getenv('BOT_TOKEN'));

// Webhook URL
define('WEBHOOK_URL', getenv('WEBHOOK_URL'));

// Supabase client initialization
class SupabaseClient {
    private $url;
    private $key;
    private $headers;
    
    public function __construct($url, $key) {
        $this->url = $url;
        $this->key = $key;
        $this->headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $key,
            'apikey: ' . $key
        ];
    }
    
    public function select($table, $filters = '', $order = '', $limit = '', $offset = '') {
        $url = $this->url . '/rest/v1/' . $table;
        
        if (!empty($filters)) {
            $url .= '?' . $filters;
        }
        
        if (!empty($order)) {
            $url .= (empty($filters) ? '?' : '&') . 'order=' . $order;
        }
        
        if (!empty($limit)) {
            $url .= (empty($filters) && empty($order) ? '?' : '&') . 'limit=' . $limit;
        }
        
        if (!empty($offset)) {
            $url .= (empty($filters) && empty($order) && empty($limit) ? '?' : '&') . 'offset=' . $offset;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    public function insert($table, $data) {
        $url = $this->url . '/rest/v1/' . $table;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    public function update($table, $data, $filters) {
        $url = $this->url . '/rest/v1/' . $table;
        
        if (!empty($filters)) {
            $url .= '?' . $filters;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    public function rpc($function, $data) {
        $url = $this->url . '/rest/v1/rpc/' . $function;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}

// Initialize Supabase client
$supabase = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);

function getSetting($key) {
    global $supabase;
    
    $result = $supabase->select('admin_settings', 'setting_key=eq.' . $key);
    
    if (isset($result[0]) && isset($result[0]['setting_value'])) {
        return $result[0]['setting_value'];
    }
    
    return null;
}
?>
