/**
 * BarByFooResource generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import Axios, {AxiosInstance, AxiosPromise} from "axios";

export default class BarByFooResource {
    private url: string;
    private token: string;
    private httpClient: AxiosInstance;

    private foo: string;

    public constructor(foo: string, baseUrl: string, token: string, httpClient?: AxiosInstance) {
        this.foo = foo;

        this.url = baseUrl + "/bar/"+foo+"";
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * Returns a collection
     *
     * @returns {AxiosPromise<EntryCollection>}
     */
    public get(): AxiosPromise<EntryCollection> {
        let params = {
            method: "GET",
        };

        return this.httpClient.get<EntryCollection>(this.url, params);
    }

    /**
     * @param {EntryCreate} data
     * @returns {AxiosPromise<EntryMessage>}
     */
    public post(data: EntryCreate): AxiosPromise<EntryMessage> {
        let params = {
            method: "POST",
        };

        return this.httpClient.post<EntryMessage>(this.url, data, params);
    }

}
