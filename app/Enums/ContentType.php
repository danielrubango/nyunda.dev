<?php

namespace App\Enums;

enum ContentType: string
{
    case InternalPost = 'internal_post';
    case ExternalPost = 'external_post';
    case CommunityLink = 'community_link';
}
