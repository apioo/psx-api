/**
 * Client automatically generated by SDKgen please do not edit this file manually
 * {@link https://sdkgen.app}
 */

import axios, {AxiosRequestConfig} from "axios";
import {ClientAbstract, TokenStoreInterface} from "sdkgen-client"
import {HttpBearer} from "sdkgen-client"
import {ClientException, UnknownStatusCodeException} from "sdkgen-client";


export default class Client extends ClientAbstract {
    public foo(): FooTag
    {
        return new FooTag(
            this.httpClient,
            this.parser
        );
    }

    public bar(): BarTag
    {
        return new BarTag(
            this.httpClient,
            this.parser
        );
    }

    public baz(): BazTag
    {
        return new BazTag(
            this.httpClient,
            this.parser
        );
    }



    public static build(token: string): Client
    {
        return new Client('http://api.foo.com', new HttpBearer(token));
    }
}
