<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\UliCMS\UliCMSVersion;

$version = new UliCMSVersion();
?>
<a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('info'); ?>"
   class="btn btn-light btn-back is-ajax"
   ><i class="fa fa-arrow-left"></i>
    <?php translate('back'); ?></a>

<h1><?php translate('license'); ?></h1>
Copyright (c) 2011 - <?php echo $version->getReleaseYear(); ?>, Ulrich Schmidt
<br>
All rights reserved.
<p>Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are
    met:
<ul>
    <li>
        Redistributions of source code must retain the above copyright
        notice, this list of conditions and the following disclaimer.</li>
    <li>
        Redistributions in binary form must reproduce the above copyright
        notice, this list of conditions and the following disclaimer in the
        documentation and/or other materials provided with the distribution.
    </li>
    <li>
        All advertising materials mentioning features or use of this
        software must display the following acknowledgement: This product
        includes software developed by UliCMS and its contributors.
    </li>
    <li>
        Neither the name of UliCMS nor the names of its contributors may be
        used to endorse or promote products derived from this software without
        specific prior written permission.
    </li>
</ul>
<p>THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
    "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
    LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
    PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
    OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
    SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
    LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
    DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
    THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
    (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
    OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.</p>
