
# /foo/:name/:type

lorem ipsum

### Path-Parameters

<div id="psx_model_Path" class="psx-object"><h4>path</h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"name"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"type"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">name</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description">Name parameter</div><dl class="psx-property-constraint"><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">type</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>Enum</dt><dd><ul class="psx-constraint-enum"><li><code>&quot;foo&quot;</code></li><li><code>&quot;bar&quot;</code></li></ul></dd></dl></td></tr></tbody></table></div>

## GET

Returns a collection

### GET Query-Parameters

<div id="psx_model_Query" class="psx-object"><h4>query</h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"startIndex"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"float"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Number</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"boolean"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Boolean</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">Date</a></span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"datetime"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">DateTime</a></span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">startIndex</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description">startIndex parameter</div><dl class="psx-property-constraint"><dt>Maximum</dt><dd><span class="psx-constraint-maximum">32</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">float</span></td><td><span class="psx-property-type">Number</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">boolean</span></td><td><span class="psx-property-type">Boolean</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">Date</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">datetime</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

### GET Response - 200 OK

<div id="psx_model_Collection" class="psx-object"><h4>collection</h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"entry"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Array (Object (<a href="#psx_model_Item" title="RFC4648">item</a>))</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">entry</span></td><td><span class="psx-property-type">Array (Object (<a href="#psx_model_Item" title="RFC4648">item</a>))</span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>
<div id="psx_model_Item" class="psx-object"><h4>item</h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">DateTime</a></span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">id</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">title</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

## POST


### POST Request

<div id="psx_model_Item" class="psx-object"><h4>item</h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">DateTime</a></span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">id</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-required">title</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-required">date</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

### POST Response - 201 Created

<div id="psx_model_Message" class="psx-object"><h4>message</h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"success"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Boolean</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"message"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">success</span></td><td><span class="psx-property-type">Boolean</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">message</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

## PUT


### PUT Request

<div id="psx_model_Item" class="psx-object"><h4>item</h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">DateTime</a></span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">id</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">title</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

### PUT Response - 200 OK

<div id="psx_model_Message" class="psx-object"><h4>message</h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"success"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Boolean</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"message"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">success</span></td><td><span class="psx-property-type">Boolean</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">message</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

## DELETE


### DELETE Request

<div id="psx_model_Item" class="psx-object"><h4>item</h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">DateTime</a></span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">id</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">title</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

### DELETE Response - 200 OK

<div id="psx_model_Message" class="psx-object"><h4>message</h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"success"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Boolean</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"message"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">success</span></td><td><span class="psx-property-type">Boolean</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">message</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

## PATCH


### PATCH Request

<div id="psx_model_Item" class="psx-object"><h4>item</h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">DateTime</a></span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">id</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">title</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6" title="RFC4648">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

### PATCH Response - 200 OK

<div id="psx_model_Message" class="psx-object"><h4>message</h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"success"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Boolean</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"message"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">success</span></td><td><span class="psx-property-type">Boolean</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">message</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>
