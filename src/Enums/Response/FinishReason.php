<?php

namespace Nahid\GoogleGenerativeAI\Enums\Response;

enum FinishReason: string
{
    case FINISH_REASON_UNSPECIFIED = 'FINISH_REASON_UNSPECIFIED';
    case MAX_TOKENS = 'MAX_TOKENS';
    case STOP = 'STOP';
    case SAFETY = 'SAFETY';
    case RECITATION = 'RECITATION';
    case LANGUAGE = 'LANGUAGE';
    case OTHER = 'OTHER';
    case BLOCKLIST = 'BLOCKLIST';
    case PROHIBITED_CONTENT = 'PROHIBITED_CONTENT';
    case SPII = 'SPII';
    case MALFORMED_FUNCTION_CALL = 'MALFORMED_FUNCTION_CALL';
}