<?php
class errPage {
    function __construct($a=array('body' => "Default Body Text",'title' => "USYVL Tools Portal")){
        $this->data = $a;
    }
    function run(){
        $buf  = "<!doctype html>\n<html lang=\"en\">\n<head\n";
        $buf .= "  <meta charset=\"utf-8\">\n";
        $buf .= "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
        $buf .= "  <title>{$this->data['title']}</title>\n";
        $buf .= "  <meta name=\"description\" content=\"USYVL Tools Portal\">\n";
        $buf .= "  <meta name=\"author\" content=\"USYVL\">\n";
        $buf .= "</head>\n";
        $buf .= "<body>\n{$this->data['body']}\n</body>\n</html>\n";
        print "$buf";
        exit();
    }
}
?>