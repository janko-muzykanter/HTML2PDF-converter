<?php

require $root.'/vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;

class pdfGenerator {

  public $bad_words=[];
  public $data = '';
  protected $to_send = [];
  protected $url = '';

  function __construct($bad_words=[]) {

    $this->url = dirname(__DIR__).'/files';
    $this->bad_words = $bad_words;
    $this->data = $_SESSION['data'] ?? [];

  }

  public function connect($json) {

    if (isset($json['words'])) {
      return $this->getWords();
    } else if (isset($json['ID'])) {
      return $this->getID($json['ID']);
    } else if (isset($json['data'])) {
      return $this->getPDF($json['data']);
    }
  }

  protected function getWords() {
    return json_encode($this->bad_words);
  }

  protected function getID($id) {

    $data = array_filter(
      $this->data,
      function ($i) use($id) {
        return $i['key']==$id;
      }
    );

    if (!empty($data))
      $this->to_send = reset($data);

    return json_encode( [
      'key' => $this->to_send['key'] ?? '',
      'url' => isset($this->to_send['url']) ? $this->return_url($this->to_send['url']) : '',
      'type' => false,
      'error' => isset($this->to_send['key']) ? '' : 'Sorry. Key does not exist.',
    ]);
  }

  protected function getPDF($json) {

    $type = $this->should_write($json);

    $html2pdf = new Html2Pdf();
    $html2pdf->writeHTML(base64_decode($this->to_send['data']));
    $html2pdf->Output($this->url."/".$this->to_send['url'], 'F');

    return json_encode( [
      'key' => $this->to_send['key'],
      'url' => $this->return_url($this->to_send['url']),
      'type' => $type,
    ]);
  }

  protected function should_write($data) {

    $data = base64_encode($data);

    $exists = array_filter(
      $this->data,
      function($i) use ($data) {
        return $i['data']==$data;
      }
    );

    if (!empty($exists)) {
      $this->to_send = reset($exists);
      return false;
    }

    $this->data[] = 
      [ 'key' => $this->add_key(),
        'url' => $this->add_key().".pdf",
        'data' => $data,
      ];

    $this->to_send = end($this->data);

    return true;
  }

  protected function add_key() {

    return 'key_'.count(array_keys($this->data));
  }

  protected function return_url($url) {

    $parent = explode('/', $_SERVER['REQUEST_URI'])[1];

    return "http://".$_SERVER['HTTP_HOST']."/".$parent."/files/".$url;
  }

  public function save_session() {
    return $_SESSION['data'] = $this->data;
  }
}

?>
