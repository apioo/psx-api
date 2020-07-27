
# /foo/:name/:type

lorem ipsum

* Path-Parameters: [Path](#Path)

## GET

Returns a collection

* Query-Parameters: [GetQuery](#GetQuery)
* Response - 200 OK: [EntryCollection](#EntryCollection)

## POST

* Request: [EntryCreate](#EntryCreate)
* Response - 201 Created: [EntryMessage](#EntryMessage)

## PUT

* Request: [EntryUpdate](#EntryUpdate)
* Response - 200 OK: [EntryMessage](#EntryMessage)

## DELETE

* Request: [EntryDelete](#EntryDelete)
* Response - 200 OK: [EntryMessage](#EntryMessage)

## PATCH

* Request: [EntryPatch](#EntryPatch)
* Response - 200 OK: [EntryMessage](#EntryMessage)



<div id="Path" class="psx-object psx-struct"><h4><a href="#Path">Path</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"name"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"type"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">name</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description">Name parameter</div><dl class="psx-property-constraint"><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">type</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>Enum</dt><dd><ul class="psx-constraint-enum"><li><code>&quot;foo&quot;</code></li><li><code>&quot;bar&quot;</code></li></ul></dd></dl></td></tr></tbody></table></div>

<div id="GetQuery" class="psx-object psx-struct"><h4><a href="#GetQuery">GetQuery</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"startIndex"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"float"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Number</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"boolean"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Boolean</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">Date</a></span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"datetime"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">DateTime</a></span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">startIndex</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description">startIndex parameter</div><dl class="psx-property-constraint"><dt>Maximum</dt><dd><span class="psx-constraint-maximum">32</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">float</span></td><td><span class="psx-property-type">Number</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">boolean</span></td><td><span class="psx-property-type">Boolean</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">Date</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">datetime</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="EntryCollection" class="psx-object psx-struct"><h4><a href="#EntryCollection">EntryCollection</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"entry"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Array (Object (<a href="#Entry">Entry</a>))</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">entry</span></td><td><span class="psx-property-type">Array (Object (<a href="#Entry">Entry</a>))</span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="Entry" class="psx-object psx-struct"><h4><a href="#Entry">Entry</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">DateTime</a></span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">id</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">title</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="EntryCreate" class="psx-object psx-struct"><h4><a href="#EntryCreate">EntryCreate</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">DateTime</a></span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">id</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-required">title</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-required">date</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="EntryMessage" class="psx-object psx-struct"><h4><a href="#EntryMessage">EntryMessage</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"success"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Boolean</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"message"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">success</span></td><td><span class="psx-property-type">Boolean</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">message</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="EntryUpdate" class="psx-object psx-struct"><h4><a href="#EntryUpdate">EntryUpdate</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">DateTime</a></span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">id</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">title</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="EntryDelete" class="psx-object psx-struct"><h4><a href="#EntryDelete">EntryDelete</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">DateTime</a></span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">id</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">title</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="EntryPatch" class="psx-object psx-struct"><h4><a href="#EntryPatch">EntryPatch</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">DateTime</a></span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">id</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">title</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a href="http://tools.ietf.org/html/rfc3339#section-5.6">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>
