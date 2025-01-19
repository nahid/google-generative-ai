<?php

namespace Nahid\GoogleGenerativeAI\Enums\Response;

enum SchemaDataType: string
{
    case STRING = 'STRING';
    case INTEGER = 'INTEGER';
    case NUMBER = 'NUMBEER';
    case BOOLEAN = 'BOOLEAN';
    case ARRAY = 'ARRAY';
    case OBJECT = 'OBJECT';
    case TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';

}
