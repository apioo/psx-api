/**
 * FooByNameAndTypeResource automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */


import app.sdkgen.client.ResourceAbstract;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.*;
import org.apache.http.client.utils.URIBuilder;
import org.apache.http.entity.ContentType;
import org.apache.http.entity.StringEntity;
import org.apache.http.util.EntityUtils;

import java.io.IOException;
import java.net.URISyntaxException;
import java.util.Map;

public class FooByNameAndTypeResource extends ResourceAbstract {
    private final String url;
    private final HttpClient httpClient;
    private final ObjectMapper objectMapper;

    private final String name;
    private final String type;

    public FooByNameAndTypeResource(String name, String type, String baseUrl, HttpClient httpClient, ObjectMapper objectMapper) {
        super(baseUrl, httpClient, objectMapper);

        this.name = name;
        this.type = type;

        this.url = baseUrl + "/foo/"+name+"/"+type+"";
        this.httpClient = httpClient;
        this.objectMapper = objectMapper;
    }

    public FooByNameAndTypeResource(String name, String type, String baseUrl, HttpClient httpClient) {
        this(name, type, baseUrl, httpClient, new ObjectMapper());
    }

    /**
     * Returns a collection
     */
    public Object postEntryOrMessage(Object data) throws URISyntaxException, IOException {
        URIBuilder builder = new URIBuilder(this.url);


        HttpPost request = new HttpPost(builder.build());
        request.addHeader("Content-Type", "application/json");
        request.setEntity(new StringEntity(this.objectMapper.writeValueAsString(data), ContentType.APPLICATION_JSON));

        HttpResponse response = this.httpClient.execute(request);

        return this.objectMapper.readValue(EntityUtils.toString(response.getEntity(), "UTF-8"), Object.class);
    }

}
