<?php

class Editor {
    const INPUT_ID_PATTERN = '%s#%s-%s';
    const INPUT_ID_REGEX = '([A-Za-z0-9]+)';
    const VIEW_LIST = false;
    const VIEW_DETAIL = true;
    public static function GetList($model) {
        $meta = new MetaInfo($model);
        if ($meta->ClassName) {
            $list = $meta->GetList();

            echo '<table style="border-collapse:collapse;">';
            Editor::RenderHeaders($meta);
            foreach ($list as $item) {
                Editor::RenderRow($meta, $item);
            }
            echo '</table>';

        }
    }

    public static function GetForm($model, $object) {

    }

    private static function RenderHeaders($meta) {
        echo '<tr>';
        foreach ($meta->Properties as $prop) {
            $title = $prop->Name;
            echo "<td>$title</td>";
        }
        echo '</tr>';
    }
    private static function RenderRow($meta, $instance) {
        $class = $meta->ClassName;
        $id = $instance->ID;
        echo "<tr ondblclick='location.assign(\"/admin/detailView/$class/$id\")'>";
        foreach ($meta->Properties as $prop) {
            echo '<td>';
            Editor::RenderInput($prop, $instance);
            echo '</td>';
        }
        echo '</tr>';
    }
    public static function RenderInput($meta, $instance,$view = Editor::VIEW_LIST) {
        $property = $meta->Name;
        $name = sprintf(Editor::INPUT_ID_PATTERN,$instance->Name(),$instance->ID,$property);
        $value = $instance->$property;
        $readonly = (strlen($meta->Key) !== 0);
        $tag = 'input';
        $type = 'text';
        $inner = null;
        // Class#ID-Property
        $attributes = array(
            'id' => $name,
            'name' => $name
        );
        if ($meta->Type === 'text') {
            $tag = 'textarea';
            $inner = $value;
            if ($inner === null) {
                $inner = '';
            }
        } elseif ($meta->IsReference) {
            $type = 'button';
            $inst = $meta->MetaInfo->GetInstance($value);
            $class = $meta->MetaInfo->ClassName;
            $attributes['onclick'] = "window.open(\"/admin/detailView/$class/$value\")";
            if (isset($inst->Name)){
                $value = $inst->Name;
            } elseif (isset($inst->Username)) {
                $value = $inst->Username;
            } elseif (isset($inst->Title)) {
                $value = $inst->Title;
            }
            $attributes['value'] = $value;
            $attributes['class'] = 'sf button';
        } else {
            switch ($meta->Type) {
                case 'timestamp':
                    $type = 'datetime-local';
                    $value = (new Datetime($value))->format("Y-m-d\TH:i:s");
                    break;
                case 'int(11)':
                    $type = 'number';
                    break;
                case 'smallint(1)':
                    if ($value != '' && $value != 0 ) {
                        $attributes['checked'] = 'checked';
                    }
                    $type = 'checkbox';
                    break;
            }
            $attributes['value'] = $value;
            $attributes['type'] = $type;
        }
        if ($readonly || $view === Editor::VIEW_LIST) {
            $attributes['readonly'] = 'readonly';
        }
        echo Html::Element($tag,$inner,$attributes);
    }
    public static function GetPropertyMap($id_string) {
        $map = array();
        preg_match_all(Editor::INPUT_ID_REGEX,$id_string,$map);
        return $map;
    }
}
