/**
 * FooNameTypeResource generated on 0000-00-00
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

public class FooNameTypeResource
{
    private final String url;
    private final String token;
    private final HttpClient httpClient;
    private final ObjectMapper objectMapper;

    private String name;
    private String type;

    public FooNameTypeResource(String name, String type, String baseUrl, String token, HttpClient httpClient)
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
    public Object postEntryOrMessage(Object data) throws URISyntaxException, IOException
    {
        URIBuilder builder = new URIBuilder(this.url);
    

        HttpPost request = new HttpPost(builder.build());
        request.addHeader("Authorization", "Bearer " + this.token);
        request.addHeader("Content-Type", "application/json");
        request.setEntity(new StringEntity(this.objectMapper.writeValueAsString(data), ContentType.APPLICATION_JSON));

        HttpResponse response = this.httpClient.execute(request);

        return this.objectMapper.readValue(EntityUtils.toString(response.getEntity(), "UTF-8"), Object.class);
    }

}
