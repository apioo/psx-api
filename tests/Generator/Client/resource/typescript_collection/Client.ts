/**
 * Client generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {ClientAbstract, TokenStoreInterface} from "sdkgen-client"
import {HttpBearer} from "sdkgen-client"

import FooGroup from "./FooGroup";
import BarGroup from "./BarGroup";

export default class Client extends ClientAbstract {
    public constructor(token: string, baseUrl: string, tokenStore: TokenStoreInterface) {
        super(baseUrl, tokenStore);

        this.credentials = new HttpBearer(token);
    }

    /**
     * Tag: foo
     * 
     * @returns FooGroup
     */
    public foo(): FooGroup
    {
        return new FooGroup(
            this.baseUrl,
            this.newHttpClient(this.credentials)
        );
    }

    /**
     * Tag: bar
     * 
     * @returns BarGroup
     */
    public bar(): BarGroup
    {
        return new BarGroup(
            this.baseUrl,
            this.newHttpClient(this.credentials)
        );
    }

}
