/**
 * FooTag automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */


import app.sdkgen.client.Exception.ClientException;
import app.sdkgen.client.Exception.UnkownStatusCodeException;
import app.sdkgen.client.TagAbstract;
import org.apache.http.HttpResponse;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.utils.URIBuilder;
import org.apache.http.entity.ContentType;
import org.apache.http.entity.StringEntity;
import org.apache.http.util.EntityUtils;

import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.HashMap;
import java.util.Map;

public class FooTag extends TagAbstract {

    /**
     * Returns a collection
     */
    public  get() throws ClientException {
        try {
            Map<String, Object> pathParams = new HashMap<>();

            Map<String, Object> queryParams = new HashMap<>();

            URIBuilder builder = new URIBuilder(this.parser.url("/foo", pathParams));
            this.parser.query(builder, queryParams);

            HttpGet request = new HttpGet(builder.build());

            HttpResponse response = this.httpClient.execute(request);
            int statusCode = response.getStatusLine().getStatusCode();

            if (statusCode >= 200 && statusCode < 300) {
                return this.parser.parse(EntityUtils.toString(response.getEntity(), "UTF-8"), .class);
            }

            switch (statusCode) {
                default:
                    throw new UnkownStatusCodeException("The server returned an unknown status code");
            }
        } catch (Exception e) {
            throw new ClientException("An unknown error occurred: " + e.getMessage());
        }
    }

    public  create( payload) throws ClientException {
        try {
            Map<String, Object> pathParams = new HashMap<>();

            Map<String, Object> queryParams = new HashMap<>();

            URIBuilder builder = new URIBuilder(this.parser.url("/foo", pathParams));
            this.parser.query(builder, queryParams);

            HttpPost request = new HttpPost(builder.build());
            request.addHeader("Content-Type", "application/json");
            request.setEntity(new StringEntity(this.objectMapper.writeValueAsString(payload), ContentType.APPLICATION_JSON));

            HttpResponse response = this.httpClient.execute(request);
            int statusCode = response.getStatusLine().getStatusCode();

            if (statusCode >= 200 && statusCode < 300) {
                return this.parser.parse(EntityUtils.toString(response.getEntity(), "UTF-8"), .class);
            }

            switch (statusCode) {
                default:
                    throw new UnkownStatusCodeException("The server returned an unknown status code");
            }
        } catch (Exception e) {
            throw new ClientException("An unknown error occurred: " + e.getMessage());
        }
    }


}
