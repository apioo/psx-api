
# /pets

foobar


## GET

List all pets

* Query-Parameters: [PetsGetQuery](#PetsGetQuery)
* Response - 200 OK: [Pets](#Pets)
* Response - 500 Internal Server Error: [Error](#Error)

## POST

Create a pet

* Request: [Pet](#Pet)
* Response - 500 Internal Server Error: [Error](#Error)



<div id="Pet" class="psx-object psx-struct"><h4><a href="#Pet">Pet</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"id"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"name"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"tag"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">id</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-required">name</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-optional">tag</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="Pets" class="psx-object psx-struct"><h4><a href="#Pets">Pets</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"pets"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Array (Object (<a href="#Pet">Pet</a>))</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">pets</span></td><td><span class="psx-property-type">Array (Object (<a href="#Pet">Pet</a>))</span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="Error" class="psx-object psx-struct"><h4><a href="#Error">Error</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"code"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
  <span class="psx-object-json-key">"message"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-required">code</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr><tr><td><span class="psx-property-name psx-property-required">message</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="PetsGetQuery" class="psx-object psx-struct"><h4><a href="#PetsGetQuery">PetsGetQuery</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"limit"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">Integer</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">limit</span></td><td><span class="psx-property-type">Integer</span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>

<div id="PetsPetIdGetQuery" class="psx-object psx-struct"><h4><a href="#PetsPetIdGetQuery">PetsPetIdGetQuery</a></h4><pre class="psx-object-json"><span class="psx-object-json-pun">{</span>
  <span class="psx-object-json-key">"petId"</span><span class="psx-object-json-pun">: </span><span class="psx-property-type">String</span><span class="psx-object-json-pun">,</span>
<span class="psx-object-json-pun">}</span></pre><table class="table psx-object-properties"><colgroup><col width="30%" /><col width="70%" /></colgroup><thead><tr><th>Field</th><th>Description</th></tr></thead><tbody><tr><td><span class="psx-property-name psx-property-optional">petId</span></td><td><span class="psx-property-type">String</span><br /><div class="psx-property-description"></div></td></tr></tbody></table></div>
