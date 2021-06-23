/**
 * FooResource generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import Axios, {AxiosInstance, AxiosPromise, Method} from "axios";
import {EntryCollection} from "./EntryCollection";
import {EntryCreate} from "./EntryCreate";
import {EntryMessage} from "./EntryMessage";

export default class FooResource {
    private url: string;
    private token: string;
    private httpClient: AxiosInstance;


    public constructor(baseUrl: string, token: string, httpClient?: AxiosInstance) {

        this.url = baseUrl + "/foo";
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
            method: <Method> "GET",
        };

        return this.httpClient.get<EntryCollection>(this.url, params);
    }

    /**
     * @param {EntryCreate} data
     * @returns {AxiosPromise<EntryMessage>}
     */
    public post(data?: EntryCreate): AxiosPromise<EntryMessage> {
        let params = {
            method: <Method> "POST",
        };

        return this.httpClient.post<EntryMessage>(this.url, data, params);
    }

}
