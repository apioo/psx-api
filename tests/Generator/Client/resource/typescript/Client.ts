/**
 * Client generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {ClientAbstract, TokenStoreInterface} from "sdkgen-client"
import {HttpBearer} from "sdkgen-client"

import FooByNameAndTypeResource from "./FooByNameAndTypeResource";

export default class Client extends ClientAbstract {
    public constructor(baseUrl: string, token: string, tokenStore: TokenStoreInterface|null = null) {
        super(baseUrl, new HttpBearer(token), tokenStore);
    }

    /**
     * Endpoint: /foo/:name/:type
     *
     * @returns FooByNameAndTypeResource
     */
    public getFooByNameAndType(name: string, type: string): FooByNameAndTypeResource
    {
        return new FooByNameAndTypeResource(
            name,
            type,
            this.baseUrl,
            this.newHttpClient()
        );
    }

}
