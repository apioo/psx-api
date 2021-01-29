/**
 * Client generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import Axios, {AxiosInstance, AxiosPromise} from "axios";
import FooResource from "./FooResource";
import BarByFooResource from "./BarByFooResource";
import BarByYearResource from "./BarByYearResource";

export default class Client {
    private baseUrl: string;
    private token: string;
    private httpClient: AxiosInstance;

    public constructor(baseUrl: string, token: string, httpClient?: AxiosInstance) {
        this.baseUrl = baseUrl;
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * Endpoint: /foo
     * 
     * @returns FooResource
     */
    public getFoo(): FooResource
    {
        return new FooResource(
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

    /**
     * Endpoint: /bar/:foo
     * 
     * @returns BarByFooResource
     */
    public getBarByFoo(foo: string): BarByFooResource
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
     * 
     * @returns BarByYearResource
     */
    public getBarByYear(year: string): BarByYearResource
    {
        return new BarByYearResource(
            year,
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

}
