/**
 * FooByNameAndTypeResource generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import Axios, {AxiosInstance, AxiosPromise} from "axios";
import {Entry} from "./Entry";
import {EntryMessage} from "./EntryMessage";

export default class FooByNameAndTypeResource {
    private url: string;
    private token: string;
    private httpClient: AxiosInstance;

    private name: string;
    private type: string;

    public constructor(name: string, type: string, baseUrl: string, token: string, httpClient?: AxiosInstance) {
        this.name = name;
        this.type = type;

        this.url = baseUrl + "/foo/"+name+"/"+type+"";
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * Returns a collection
     *
     * @param {Entry | EntryMessage} data
     * @returns {AxiosPromise<Entry | EntryMessage>}
     */
    public postEntryOrMessage(data?: Entry | EntryMessage): AxiosPromise<Entry | EntryMessage> {
        let params = {
            method: "POST",
            headers: {
                'Authorization': 'Bearer ' + this.token
            },
        };

        return this.httpClient.post<Entry | EntryMessage>(this.url, data, params);
    }

}
