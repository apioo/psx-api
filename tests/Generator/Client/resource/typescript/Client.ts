/**
 * Client automatically generated by SDKgen please do not edit this file manually
 * {@link https://sdkgen.app}
 */

import axios, {AxiosRequestConfig} from "axios";
import {ClientAbstract, TokenStoreInterface} from "sdkgen-client"
import {HttpBearer} from "sdkgen-client"
import {ClientException, UnknownStatusCodeException} from "sdkgen-client";

import {EntryCollection} from "./EntryCollection";
import {EntryCreate} from "./EntryCreate";
import {EntryDelete} from "./EntryDelete";
import {EntryMessage} from "./EntryMessage";
import {EntryPatch} from "./EntryPatch";
import {EntryUpdate} from "./EntryUpdate";

export default class Client extends ClientAbstract {
    /**
     * Returns a collection
     *
     * @returns {Promise<EntryCollection>}
     * @throws {ClientException}
     */
    public async get(name: string, type: string, startIndex?: number, float?: number, boolean?: boolean, date?: string, datetime?: string): Promise<EntryCollection> {
        const url = this.parser.url('/foo/:name/:type', {
            'name': name,
            'type': type,
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'GET',
            params: this.parser.query({
                'startIndex': startIndex,
                'float': float,
                'boolean': boolean,
                'date': date,
                'datetime': datetime,
            }),
        };

        try {
            const response = await this.httpClient.request<EntryCollection>(params);
            return response.data;
        } catch (error) {
            if (axios.isAxiosError(error) && error.response) {
                switch (error.response.status) {
                    default:
                        throw new UnknownStatusCodeException('The server returned an unknown status code');
                }
            }

            throw new ClientException('An unknown error occurred: ' + String(error));
        }
    }

    /**
     * @returns {Promise<EntryMessage>}
     * @throws {EntryMessageException}
     * @throws {EntryMessageException}
     * @throws {ClientException}
     */
    public async create(name: string, type: string, payload: EntryCreate): Promise<EntryMessage> {
        const url = this.parser.url('/foo/:name/:type', {
            'name': name,
            'type': type,
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'POST',
            params: this.parser.query({
            }),
            data: payload
        };

        try {
            const response = await this.httpClient.request<EntryMessage>(params);
            return response.data;
        } catch (error) {
            if (axios.isAxiosError(error) && error.response) {
                switch (error.response.status) {
                    case 400:
                        throw new EntryMessageException(error.response.data);
                    case 500:
                        throw new EntryMessageException(error.response.data);
                    default:
                        throw new UnknownStatusCodeException('The server returned an unknown status code');
                }
            }

            throw new ClientException('An unknown error occurred: ' + String(error));
        }
    }

    /**
     * @returns {Promise<EntryMessage>}
     * @throws {ClientException}
     */
    public async update(name: string, type: string, payload: EntryUpdate): Promise<EntryMessage> {
        const url = this.parser.url('/foo/:name/:type', {
            'name': name,
            'type': type,
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'PUT',
            params: this.parser.query({
            }),
            data: payload
        };

        try {
            const response = await this.httpClient.request<EntryMessage>(params);
            return response.data;
        } catch (error) {
            if (axios.isAxiosError(error) && error.response) {
                switch (error.response.status) {
                    default:
                        throw new UnknownStatusCodeException('The server returned an unknown status code');
                }
            }

            throw new ClientException('An unknown error occurred: ' + String(error));
        }
    }

    /**
     * @returns {Promise<EntryMessage>}
     * @throws {ClientException}
     */
    public async delete(name: string, type: string): Promise<EntryMessage> {
        const url = this.parser.url('/foo/:name/:type', {
            'name': name,
            'type': type,
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'DELETE',
            params: this.parser.query({
            }),
        };

        try {
            const response = await this.httpClient.request<EntryMessage>(params);
            return response.data;
        } catch (error) {
            if (axios.isAxiosError(error) && error.response) {
                switch (error.response.status) {
                    default:
                        throw new UnknownStatusCodeException('The server returned an unknown status code');
                }
            }

            throw new ClientException('An unknown error occurred: ' + String(error));
        }
    }

    /**
     * @returns {Promise<EntryMessage>}
     * @throws {ClientException}
     */
    public async patch(name: string, type: string, payload: EntryPatch): Promise<EntryMessage> {
        const url = this.parser.url('/foo/:name/:type', {
            'name': name,
            'type': type,
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'PATCH',
            params: this.parser.query({
            }),
            data: payload
        };

        try {
            const response = await this.httpClient.request<EntryMessage>(params);
            return response.data;
        } catch (error) {
            if (axios.isAxiosError(error) && error.response) {
                switch (error.response.status) {
                    default:
                        throw new UnknownStatusCodeException('The server returned an unknown status code');
                }
            }

            throw new ClientException('An unknown error occurred: ' + String(error));
        }
    }



    public static build(token: string): Client
    {
        return new Client('http://api.foo.com', new HttpBearer(token));
    }
}
