/**
 * FooByNameAndTypeResource generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {AxiosInstance, AxiosRequestConfig, AxiosResponse} from "axios";
import {ResourceAbstract} from "sdkgen-client"
import {Entry} from "./Entry";
import {EntryMessage} from "./EntryMessage";

export default class FooByNameAndTypeResource extends ResourceAbstract {
    private url: string;

    private name: string;
    private type: string;

    public constructor(name: string, type: string, baseUrl: string, httpClient: AxiosInstance) {
        super(baseUrl, httpClient);

        this.name = name;
        this.type = type;

        this.url = baseUrl + "/foo/"+name+"/"+type+"";
    }

    /**
     * Returns a collection
     *
     * @param {Entry | EntryMessage} data
     * @returns {AxiosResponse<Entry | EntryMessage>}
     */
    public async postEntryOrMessage(data?: Entry | EntryMessage): AxiosResponse<Entry | EntryMessage> {
        let params: AxiosRequestConfig = {
            method: 'POST',
            responseType: 'json',
            headers: {
                Accept: 'application/json',
            },
        };

        return this.httpClient.post<Entry | EntryMessage>(this.url, data, params);
    }

}
