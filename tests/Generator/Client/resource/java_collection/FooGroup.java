/**
 * FooGroup generated on 0000-00-00
 * @see https://sdkgen.app
 */


import app.sdkgen.client.ResourceAbstract;
import org.apache.http.client.HttpClient;
import org.apache.http.impl.client.HttpClientBuilder;

public class FooGroup extends ResourceAbstract
{
    private final String baseUrl;
    private final String token;
    private final HttpClient httpClient;

    public FooGroup(String baseUrl, String token, HttpClient httpClient)
    {
        super(baseUrl, httpClient);
        this.baseUrl = baseUrl;
        this.token = token;
        this.httpClient = httpClient != null ? httpClient : HttpClientBuilder.create().build();
    }

    /**
     * Endpoint: /foo
     */
    public FooResource getFoo()
    {
        return new FooResource(
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

}
