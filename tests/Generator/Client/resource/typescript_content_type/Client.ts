/**
 * Client automatically generated by SDKgen please do not edit this file manually
 * {@link https://sdkgen.app}
 */

import axios, {AxiosRequestConfig} from "axios";
import {ClientAbstract, CredentialsInterface, TokenStoreInterface} from "sdkgen-client"
import {Anonymous} from "sdkgen-client"
import {ClientException, UnknownStatusCodeException} from "sdkgen-client";

import {BinaryException} from "./BinaryException";
import {FormException} from "./FormException";
import {JsonException} from "./JsonException";
import {MultipartException} from "./MultipartException";
import {TextException} from "./TextException";
import {XmlException} from "./XmlException";

export class Client extends ClientAbstract {
    /**
     * @returns {Promise<ArrayBuffer>}
     * @throws {BinaryException}
     * @throws {ClientException}
     */
    public async binary(body: ArrayBuffer): Promise<ArrayBuffer> {
        const url = this.parser.url('/binary', {
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'POST',
            headers: {
                'Content-Type': 'application/octet-stream',
            },
            params: this.parser.query({
            }, [
            ]),
            responseType: 'arraybuffer',
            data: body
        };

        try {
            const response = await this.httpClient.request<ArrayBuffer>(params);
            return response.data;
        } catch (error) {
            if (error instanceof ClientException) {
                throw error;
            } else if (axios.isAxiosError(error) && error.response) {
                const statusCode = error.response.status;

                if (statusCode >= 0 && statusCode <= 999) {
                    throw new BinaryException(error.response.data);
                }

                throw new UnknownStatusCodeException('The server returned an unknown status code');
            } else {
                throw new ClientException('An unknown error occurred: ' + String(error));
            }
        }
    }

    /**
     * @returns {Promise<URLSearchParams>}
     * @throws {FormException}
     * @throws {ClientException}
     */
    public async form(body: URLSearchParams): Promise<URLSearchParams> {
        const url = this.parser.url('/form', {
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            params: this.parser.query({
            }, [
            ]),
            data: body
        };

        try {
            const response = await this.httpClient.request<URLSearchParams>(params);
            return response.data;
        } catch (error) {
            if (error instanceof ClientException) {
                throw error;
            } else if (axios.isAxiosError(error) && error.response) {
                const statusCode = error.response.status;

                if (statusCode >= 500 && statusCode <= 599) {
                    throw new FormException(error.response.data);
                }

                throw new UnknownStatusCodeException('The server returned an unknown status code');
            } else {
                throw new ClientException('An unknown error occurred: ' + String(error));
            }
        }
    }

    /**
     * @returns {Promise<any>}
     * @throws {JsonException}
     * @throws {ClientException}
     */
    public async json(body: any): Promise<any> {
        const url = this.parser.url('/json', {
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            params: this.parser.query({
            }, [
            ]),
            responseType: 'json',
            data: body
        };

        try {
            const response = await this.httpClient.request<any>(params);
            return response.data;
        } catch (error) {
            if (error instanceof ClientException) {
                throw error;
            } else if (axios.isAxiosError(error) && error.response) {
                const statusCode = error.response.status;

                if (statusCode >= 400 && statusCode <= 499) {
                    throw new JsonException(error.response.data);
                }

                throw new UnknownStatusCodeException('The server returned an unknown status code');
            } else {
                throw new ClientException('An unknown error occurred: ' + String(error));
            }
        }
    }

    /**
     * @returns {Promise<FormData>}
     * @throws {MultipartException}
     * @throws {ClientException}
     */
    public async multipart(body: FormData): Promise<FormData> {
        const url = this.parser.url('/multipart', {
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'POST',
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            params: this.parser.query({
            }, [
            ]),
            data: body
        };

        try {
            const response = await this.httpClient.request<FormData>(params);
            return response.data;
        } catch (error) {
            if (error instanceof ClientException) {
                throw error;
            } else if (axios.isAxiosError(error) && error.response) {
                const statusCode = error.response.status;

                if (statusCode === 500) {
                    throw new MultipartException(error.response.data);
                }

                throw new UnknownStatusCodeException('The server returned an unknown status code');
            } else {
                throw new ClientException('An unknown error occurred: ' + String(error));
            }
        }
    }

    /**
     * @returns {Promise<string>}
     * @throws {TextException}
     * @throws {ClientException}
     */
    public async text(body: string): Promise<string> {
        const url = this.parser.url('/text', {
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'POST',
            headers: {
                'Content-Type': 'text/plain',
            },
            params: this.parser.query({
            }, [
            ]),
            responseType: 'text',
            data: body
        };

        try {
            const response = await this.httpClient.request<string>(params);
            return response.data;
        } catch (error) {
            if (error instanceof ClientException) {
                throw error;
            } else if (axios.isAxiosError(error) && error.response) {
                const statusCode = error.response.status;

                if (statusCode === 500) {
                    throw new TextException(error.response.data);
                }

                throw new UnknownStatusCodeException('The server returned an unknown status code');
            } else {
                throw new ClientException('An unknown error occurred: ' + String(error));
            }
        }
    }

    /**
     * @returns {Promise<XMLDocument>}
     * @throws {XmlException}
     * @throws {ClientException}
     */
    public async xml(body: XMLDocument): Promise<XMLDocument> {
        const url = this.parser.url('/xml', {
        });

        let params: AxiosRequestConfig = {
            url: url,
            method: 'POST',
            headers: {
                'Content-Type': 'application/xml',
            },
            params: this.parser.query({
            }, [
            ]),
            responseType: 'document',
            data: body
        };

        try {
            const response = await this.httpClient.request<XMLDocument>(params);
            return response.data;
        } catch (error) {
            if (error instanceof ClientException) {
                throw error;
            } else if (axios.isAxiosError(error) && error.response) {
                const statusCode = error.response.status;

                if (statusCode === 500) {
                    throw new XmlException(error.response.data);
                }

                throw new UnknownStatusCodeException('The server returned an unknown status code');
            } else {
                throw new ClientException('An unknown error occurred: ' + String(error));
            }
        }
    }




    public static buildAnonymous(): Client
    {
        return new Client('http://api.foo.com', new Anonymous());
    }
}
