/**
 * Client automatically generated by SDKgen please do not edit this file manually
 * {@link https://sdkgen.app}
 */

import axios, {AxiosRequestConfig} from "axios";
import {ClientAbstract, TokenStoreInterface} from "sdkgen-client"
import {HttpBearer} from "sdkgen-client"
import {ClientException, UnknownStatusCodeException} from "sdkgen-client";

import {TestRequest} from "./TestRequest";
import {TestResponse} from "./TestResponse";

export default class Client extends ClientAbstract {
    /**
     * Returns a collection
     *
     * @returns {Promise<TestResponse>}
     * @throws {ClientException}
     */
    public async getAll(startIndex?: number, count?: number, search?: string): Promise<TestResponse> {
        const url = this.parser.url('/anything', {
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'GET',
            params: this.parser.query({
                'startIndex': startIndex,
                'count': count,
                'search': search,
            }),
        };

        try {
            const response = await this.httpClient.request<TestResponse>(params);
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
     * Creates a new product
     *
     * @returns {Promise<TestResponse>}
     * @throws {ClientException}
     */
    public async create(payload: TestRequest): Promise<TestResponse> {
        const url = this.parser.url('/anything', {
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'POST',
            params: this.parser.query({
            }),
            data: payload
        };

        try {
            const response = await this.httpClient.request<TestResponse>(params);
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
     * Updates an existing product
     *
     * @returns {Promise<TestResponse>}
     * @throws {ClientException}
     */
    public async update(id: number, payload: TestRequest): Promise<TestResponse> {
        const url = this.parser.url('/anything/:id', {
            'id': id,
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'PUT',
            params: this.parser.query({
            }),
            data: payload
        };

        try {
            const response = await this.httpClient.request<TestResponse>(params);
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
     * Patches an existing product
     *
     * @returns {Promise<TestResponse>}
     * @throws {ClientException}
     */
    public async patch(id: number, payload: TestRequest): Promise<TestResponse> {
        const url = this.parser.url('/anything/:id', {
            'id': id,
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'PATCH',
            params: this.parser.query({
            }),
            data: payload
        };

        try {
            const response = await this.httpClient.request<TestResponse>(params);
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
     * Deletes an existing product
     *
     * @returns {Promise<TestResponse>}
     * @throws {ClientException}
     */
    public async delete(id: number): Promise<TestResponse> {
        const url = this.parser.url('/anything/:id', {
            'id': id,
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'DELETE',
            params: this.parser.query({
            }),
        };

        try {
            const response = await this.httpClient.request<TestResponse>(params);
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
        return new Client('http://127.0.0.1:8081', new HttpBearer(token));
    }
}
