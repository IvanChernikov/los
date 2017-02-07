<?php
class RssFeed extends ModelBase {
    public $Name;
    public $Url;
    public $Map;

    public function ParseToHTML() {
        $xml = file_get_contents($this->Url);
        $parsed = new SimpleXMLElement($xml);
        $map = json_decode($this->Map);
        return $this->MapXML($parsed, $map);
    }

    private function MapXML($node, $map) {
        $html = '<ul style="font-size: 0.95em; line-height:1.1em;">';
        foreach ($map as $key => $value) {
            $html .= '<li>';
            // $html .= sprintf('<h2>%s => %s</h2>', $key, gettype($value));
            if (isset($node->$key)) {
                switch(gettype($value)) {
                    case 'string':
                        $html .= sprintf('<%1$s%3$s>%2$s</%1$s>',$value, $node->$key,($value === 'a' ? ' href="'. $node->$key . '" target="_blank"' : ''));
                        break;
                    case 'object':
                        $html .= $this->MapXML($node->$key, $map->$key);
                        break;
                    case 'array':
                        //var_dump($map->$key)
                        foreach ($node->$key as $item) {
                            $html .= '<div class="sf box darker round mb">'.$this->MapXML($item, $value[0]).'</div>';
                        }
                        break;
                }
            }
            $html .= '</li>';
        }
        return $html . '</ul>';
    }
}
