<?php

namespace VLF;

const OBJECT_DEFINITION = 1;
const PROPERTY_SET      = 2;
const METHOD_CALL       = 4;
const RUNTIME_EXECUTION = 8;
const STYLES_IMPORTING  = 16;
const STYLE_DEFINITION  = 32;

require 'bin/AST/Node.php';
require 'bin/AST/Tree.php';
require 'bin/Stack.php';
require 'bin/Parser.php';
require 'bin/Translator.php';
require 'bin/Interpreter.php';
require 'bin/VST/Parser.php';
require 'bin/VST/Interpreter.php';
