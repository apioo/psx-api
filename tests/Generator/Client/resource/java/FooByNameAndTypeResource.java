/**
 * FooByNameAndTypeResource generated on 0000-00-00
 * {@link https://github.com/apioo}
 */


import com.fasterxml.jackson.databind.ObjectMapper;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.*;
import org.apache.http.client.utils.URIBuilder;
import org.apache.http.entity.ContentType;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.HttpClientBuilder;
import org.apache.http.util.EntityUtils;

import java.io.IOException;
import java.net.URISyntaxException;
import java.util.Map;

public class FooByNameAndTypeResource
{
    private final String url;
    private final String token;
    private final HttpClient httpClient;
    private final ObjectMapper objectMapper;

    private String name;
    private String type;

    public FooByNameAndTypeResource(String name, String type, String baseUrl, String token, HttpClient httpClient)
    {
        this.name = name;
        this.type = type;

        this.url = baseUrl + "/foo/"+name+"/"+type+"";
        this.token = token;
        this.httpClient = httpClient != null ? httpClient : HttpClientBuilder.create().build();
        this.objectMapper = new ObjectMapper();
    }

    /**
     * Returns a collection
     */
    public EntryCollection listFoo(GetQuery query) throws URISyntaxException, IOException
    {
        URIBuilder builder = new URIBuilder(this.url);
    
        Map<String, Object> params = this.objectMapper.convertValue(query, Map.class);
        for(Map.Entry<String, Object> entry : params.entrySet()) {
            builder.setParameter(entry.getKey(), entry.getValue().toString());
        }

        HttpGet request = new HttpGet(builder.build());

        HttpResponse response = this.httpClient.execute(request);

        return this.objectMapper.readValue(EntityUtils.toString(response.getEntity(), "UTF-8"), EntryCollection.class);
    }

    public EntryMessage createFoo(EntryCreate data) throws URISyntaxException, IOException
    {
        URIBuilder builder = new URIBuilder(this.url);
    

        HttpPost request = new HttpPost(builder.build());
        request.addHeader("Content-Type", "application/json");
        request.setEntity(new StringEntity(this.objectMapper.writeValueAsString(data), ContentType.APPLICATION_JSON));

        HttpResponse response = this.httpClient.execute(request);

        return this.objectMapper.readValue(EntityUtils.toString(response.getEntity(), "UTF-8"), EntryMessage.class);
    }

    public EntryMessage put(EntryUpdate data) throws URISyntaxException, IOException
    {
        URIBuilder builder = new URIBuilder(this.url);
    

        HttpPut request = new HttpPut(builder.build());
        request.addHeader("Content-Type", "application/json");
        request.setEntity(new StringEntity(this.objectMapper.writeValueAsString(data), ContentType.APPLICATION_JSON));

        HttpResponse response = this.httpClient.execute(request);

        return this.objectMapper.readValue(EntityUtils.toString(response.getEntity(), "UTF-8"), EntryMessage.class);
    }

    public EntryMessage delete() throws URISyntaxException, IOException
    {
        URIBuilder builder = new URIBuilder(this.url);
    

        HttpDelete request = new HttpDelete(builder.build());

        HttpResponse response = this.httpClient.execute(request);

        return this.objectMapper.readValue(EntityUtils.toString(response.getEntity(), "UTF-8"), EntryMessage.class);
    }

    public EntryMessage patch(EntryPatch data) throws URISyntaxException, IOException
    {
        URIBuilder builder = new URIBuilder(this.url);
    

        HttpPatch request = new HttpPatch(builder.build());
        request.addHeader("Content-Type", "application/json");
        request.setEntity(new StringEntity(this.objectMapper.writeValueAsString(data), ContentType.APPLICATION_JSON));

        HttpResponse response = this.httpClient.execute(request);

        return this.objectMapper.readValue(EntityUtils.toString(response.getEntity(), "UTF-8"), EntryMessage.class);
    }

}
