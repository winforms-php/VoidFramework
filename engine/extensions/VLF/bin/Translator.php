<?php

namespace VLF;

class Translator
{
    public static function translate (AST $tree): string
    {
        $code = "<?php\n\nnamespace VoidEngine;\n\n";
        $definedObjects = [];

        foreach ($tree->getNodes () as $node)
            $code .= self::translateNode ($node, null, $definedObjects) ."\n";

        return $code;
    }

    protected static function translateNode (Node $node, Node $owner = null, array &$definedObjects = []): string
    {
        $code = '';

        switch ($node->type)
        {
            case RUNTIME_EXECUTION:
                $code .= self::formatLine ($node->args['code'], $definedObjects) ."\n\n";

                break;

            case OBJECT_DEFINITION:
                if (isset ($definedObjects[$node->args['name']]))
                    break;
                
                $code .= '$'. $node->args['name'] .' = new '. $node->args['class'] .' ('. implode (', ', self::processArgs ($node->args['args'], $definedObjects)) .');' ."\n". '$'. $node->args['name'] .'->name = \''. $node->args['name'] .'\';' ."\n";

                $definedObjects[$node->args['name']] = $node->args['name'];

                break;

            case PROPERTY_SET:
                $preset = '';

                if (preg_match ('/function \((.*)\) use \((.*)\)/', $node->args['property_value']))
                {
                    $use = substr ($node->args['property_value'], strpos ($node->args['property_value'], 'use'));
                    $use = $ouse = substr ($use, ($pos = strpos ($use, '(') + 1), strpos ($use, ')') - $pos);
                    $use = explode (' ', $use);

                    foreach ($use as $id => $useParam)  
                        if (isset ($definedObjects[$useParam]) && $use[$id + 1][0] == '$')
                        {
                            $fname = $use[$id + 1];

                            if (substr ($fname, strlen ($fname) - 1) == ',')
                                $fname = substr ($fname, 0, -1);

                            $preset .= "$fname = $useParam; ";

                            unset ($use[$id]);
                        }

                    $preset = self::formatLine ($preset, $definedObjects) ."\n";

                    $node->args['property_value'] = self::formatLine (str_replace ($ouse, join (' ', $use), $node->args['property_value']), $definedObjects);
                }
                
                $code .= $preset .'$'. $owner->args['name'] .'->'. $node->args['property_name'] .' = '. self::formatLine ($node->args['property_value'], $definedObjects) .';' ."\n";

                break;

            case METHOD_CALL:
                $code .= '$'. $owner->args['name'] .'->'. $node->args['method_name'] .' ('. implode (', ', self::processArgs ($node->args['method_args'], $definedObjects)) .');' ."\n";

                break;
        }

        foreach ($node->getNodes () as $subnode)
            $code .= self::translateNode ($subnode, $node, $definedObjects);

        return $code;
    }

    protected static function processArgs (array $args, array $definedObjects = []): array
    {
        $newArgs = [];

        foreach ($args as $arg)
            $newArgs[] = self::formatLine ($arg, $definedObjects);

        return $newArgs;
    }

    protected static function formatLine (string $line, array $objects = []): string
    {
        if (sizeof ($objects) > 0)
        {
            $len     = strlen ($line);
            $newLine = '';

            $replacement = array_map (function ($object)
            {
                return '$'. $object;
            }, $objects);

            $replacement = array_map (function ($name)
            {
                return strlen ($name = trim ($name)) + substr_count ($name, '_');
            }, $omap = array_flip ($replacement));

            arsort ($replacement);

            $nReplacement = [];

            foreach ($replacement as $replaceTo => $nLn)
                $nReplacement[$omap[$replaceTo]] = $replaceTo;

            $replacement = $nReplacement;
            $blacklist   = array_flip (['\'', '"', '$']);

            for ($i = 0; $i < $len; ++$i)
            {
                $replaced = false;

                foreach ($replacement as $name => $replaceAt)
                    if (substr ($line, $i, ($l = strlen ($name))) == $name && !isset ($blacklist[$line[$i - 1]]))
                    {
                        $newLine .= $replaceAt;

                        $i += $l - 1;
                        $replaced = true;

                        break;
                    }

                if (!$replaced)
                    $newLine .= $line[$i];
            }

            $line = $newLine;
        }

        return $line;
    }
}
