# get
`GET /foo/:name/:type`

> Returns a collection

## Request

<table><colgroup><col width="40%" /><col width="40%" /><col width="20%" /></colgroup><thead><tr><th>Name</th><th>Type</th><th>Location</th></tr></thead><tbody><tr><td>name</td><td>String</td><td>path</td></tr><tr><td>type</td><td>String</td><td>path</td></tr><tr><td>startIndex</td><td>Integer</td><td>query</td></tr><tr><td>float</td><td>Number</td><td>query</td></tr><tr><td>boolean</td><td>Boolean</td><td>query</td></tr><tr><td>date</td><td>Date</td><td>query</td></tr><tr><td>datetime</td><td>DateTime</td><td>query</td></tr></tbody></table>

## Response

<table><colgroup><col width="40%" /><col width="60%" /></colgroup><thead><tr><th>Status-Code</th><th>Type</th></tr></thead><tbody><tr><td>200</td><td>EntryCollection</td></tr></tbody></table>

# create
`POST /foo/:name/:type`


## Request

<table><colgroup><col width="40%" /><col width="40%" /><col width="20%" /></colgroup><thead><tr><th>Name</th><th>Type</th><th>Location</th></tr></thead><tbody><tr><td>name</td><td>String</td><td>path</td></tr><tr><td>type</td><td>String</td><td>path</td></tr><tr><td>payload</td><td>EntryCreate</td><td>body</td></tr></tbody></table>

## Response

<table><colgroup><col width="40%" /><col width="60%" /></colgroup><thead><tr><th>Status-Code</th><th>Type</th></tr></thead><tbody><tr><td>201</td><td>EntryMessage</td></tr><tr><td>400</td><td>EntryMessage</td></tr><tr><td>500</td><td>EntryMessage</td></tr></tbody></table>

# update
`PUT /foo/:name/:type`


## Request

<table><colgroup><col width="40%" /><col width="40%" /><col width="20%" /></colgroup><thead><tr><th>Name</th><th>Type</th><th>Location</th></tr></thead><tbody><tr><td>name</td><td>String</td><td>path</td></tr><tr><td>type</td><td>String</td><td>path</td></tr><tr><td>payload</td><td>EntryUpdate</td><td>body</td></tr></tbody></table>

## Response

<table><colgroup><col width="40%" /><col width="60%" /></colgroup><thead><tr><th>Status-Code</th><th>Type</th></tr></thead><tbody><tr><td>200</td><td>EntryMessage</td></tr></tbody></table>

# delete
`DELETE /foo/:name/:type`


## Request

<table><colgroup><col width="40%" /><col width="40%" /><col width="20%" /></colgroup><thead><tr><th>Name</th><th>Type</th><th>Location</th></tr></thead><tbody><tr><td>name</td><td>String</td><td>path</td></tr><tr><td>type</td><td>String</td><td>path</td></tr></tbody></table>

## Response

<table><colgroup><col width="40%" /><col width="60%" /></colgroup><thead><tr><th>Status-Code</th><th>Type</th></tr></thead><tbody></tbody></table>

# patch
`PATCH /foo/:name/:type`


## Request

<table><colgroup><col width="40%" /><col width="40%" /><col width="20%" /></colgroup><thead><tr><th>Name</th><th>Type</th><th>Location</th></tr></thead><tbody><tr><td>name</td><td>String</td><td>path</td></tr><tr><td>type</td><td>String</td><td>path</td></tr><tr><td>payload</td><td>EntryPatch</td><td>body</td></tr></tbody></table>

## Response

<table><colgroup><col width="40%" /><col width="60%" /></colgroup><thead><tr><th>Status-Code</th><th>Type</th></tr></thead><tbody><tr><td>200</td><td>EntryMessage</td></tr></tbody></table>


<div id="EntryCollection" class="psx-object psx-struct"><h4><a class="psx-type-link" data-name="EntryCollection">EntryCollection</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"entry"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Array (Entry)</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">entry</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="Array (Entry)">Array (Entry)</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="Entry" class="psx-object psx-struct"><h4><a class="psx-type-link" data-name="Entry">Entry</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">DateTime</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">id</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="Integer">Integer</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="Integer">Integer</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">title</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="String">String</a></span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="DateTime">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="EntryMessage" class="psx-object psx-struct"><h4><a class="psx-type-link" data-name="EntryMessage">EntryMessage</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"success"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Boolean</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"message"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">success</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="Boolean">Boolean</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">message</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="String">String</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="EntryCreate" class="psx-object psx-struct"><h4><a class="psx-type-link" data-name="EntryCreate">EntryCreate</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">DateTime</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">id</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="Integer">Integer</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="Integer">Integer</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-required">title</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="String">String</a></span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-required">date</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="DateTime">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="EntryUpdate" class="psx-object psx-struct"><h4><a class="psx-type-link" data-name="EntryUpdate">EntryUpdate</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">DateTime</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">id</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="Integer">Integer</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="Integer">Integer</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">title</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="String">String</a></span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="DateTime">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="EntryDelete" class="psx-object psx-struct"><h4><a class="psx-type-link" data-name="EntryDelete">EntryDelete</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">DateTime</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">id</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="Integer">Integer</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="Integer">Integer</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">title</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="String">String</a></span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="DateTime">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="EntryPatch" class="psx-object psx-struct"><h4><a class="psx-type-link" data-name="EntryPatch">EntryPatch</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"userId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"title"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"date"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">DateTime</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">id</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="Integer">Integer</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">userId</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="Integer">Integer</a></span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">title</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="String">String</a></span><br /><div class="psx-property-description"></div><dl class="psx-property-constraint"><dt>MinLength</dt><dd><span class="psx-constraint-minlength">3</span></dd><dt>MaxLength</dt><dd><span class="psx-constraint-maxlength">16</span></dd><dt>Pattern</dt><dd><span class="psx-constraint-pattern">[A-z]+</span></dd></dl></td></tr><tr><td><span class="psx-property-name psx-property-optional">date</span></td><td><span class="psx-property-type"><a class="psx-type-link" data-name="DateTime">DateTime</a></span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>
