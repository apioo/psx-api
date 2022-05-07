/**
 * FooGroup generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {ResourceAbstract} from "sdkgen-client"
import FooResource from "./FooResource";

export default class FooGroup extends ResourceAbstract {
    /**
     * Endpoint: /foo
     */
    public getFoo(): FooResource
    {
        return new FooResource(
            this.baseUrl,
            this.httpClient
        );
    }

}
