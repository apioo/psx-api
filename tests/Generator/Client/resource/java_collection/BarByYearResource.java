/**
 * BarByYearResource generated on 0000-00-00
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
import org.apache.http.impl.client.HttpClientBuilder;
import org.apache.http.util.EntityUtils;

import java.io.IOException;
import java.net.URISyntaxException;
import java.util.Map;

public class BarByYearResource extends ResourceAbstract
{
    private final String url;
    private final HttpClient httpClient;
    private final ObjectMapper objectMapper;

    private String year;

    public BarByYearResource(String year, String baseUrl, HttpClient httpClient, ObjectMapper objectMapper)
    {
        super(baseUrl, httpClient, objectMapper);

        this.year = year;

        this.url = baseUrl + "/bar/"+year+"";
        this.token = token;
        this.httpClient = httpClient != null ? httpClient : HttpClientBuilder.create().build();
        this.objectMapper = new ObjectMapper();
    }

    /**
     * Returns a collection
     */
    public EntryCollection get() throws URISyntaxException, IOException
    {
        URIBuilder builder = new URIBuilder(this.url);
    

        HttpGet request = new HttpGet(builder.build());

        HttpResponse response = this.httpClient.execute(request);

        return this.objectMapper.readValue(EntityUtils.toString(response.getEntity(), "UTF-8"), EntryCollection.class);
    }

    public EntryMessage post(EntryCreate data) throws URISyntaxException, IOException
    {
        URIBuilder builder = new URIBuilder(this.url);
    

        HttpPost request = new HttpPost(builder.build());
        request.addHeader("Content-Type", "application/json");
        request.setEntity(new StringEntity(this.objectMapper.writeValueAsString(data), ContentType.APPLICATION_JSON));

        HttpResponse response = this.httpClient.execute(request);

        return this.objectMapper.readValue(EntityUtils.toString(response.getEntity(), "UTF-8"), EntryMessage.class);
    }

}
