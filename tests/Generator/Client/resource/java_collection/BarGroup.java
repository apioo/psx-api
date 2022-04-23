/**
 * BarGroup generated on 0000-00-00
 * @see https://sdkgen.app
 */


import app.sdkgen.client.ResourceAbstract;
import org.apache.http.client.HttpClient;
import org.apache.http.impl.client.HttpClientBuilder;

public class BarGroup extends ResourceAbstract
{
    private final String baseUrl;
    private final String token;
    private final HttpClient httpClient;

    public BarGroup(String baseUrl, String token, HttpClient httpClient)
    {
        super(baseUrl, httpClient);
        this.baseUrl = baseUrl;
        this.token = token;
        this.httpClient = httpClient != null ? httpClient : HttpClientBuilder.create().build();
    }

    /**
     * Endpoint: /bar/:foo
     */
    public BarByFooResource getBarByFoo(String foo)
    {
        return new BarByFooResource(
            foo,
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

    /**
     * Endpoint: /bar/$year<[0-9]+>
     */
    public BarByYearResource getBarByYear(String year)
    {
        return new BarByYearResource(
            year,
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

}
