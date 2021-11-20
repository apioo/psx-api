/**
 * FooByNameAndTypeResource generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import Axios, {AxiosInstance, AxiosPromise, Method} from "axios";
import {Entry} from "./Entry";
import {EntryMessage} from "./EntryMessage";

export default class FooByNameAndTypeResource extends ResourceAbstract {
    private url: string;

    private name: string;
    private type: string;

    public constructor(name: string, type: string, baseUrl: string, httpClient?: AxiosInstance) {
        super(baseUrl, httpClient);

        this.name = name;
        this.type = type;

        this.url = baseUrl + "/foo/"+name+"/"+type+"";
    }

    /**
     * Returns a collection
     *
     * @param {Entry | EntryMessage} data
     * @returns {AxiosPromise<Entry | EntryMessage>}
     */
    public postEntryOrMessage(data?: Entry | EntryMessage): AxiosPromise<Entry | EntryMessage> {
        let params = {
            method: <Method> "POST",
        };

        return this.httpClient.post<Entry | EntryMessage>(this.url, data, params);
    }

}