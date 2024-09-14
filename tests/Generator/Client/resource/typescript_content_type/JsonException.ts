/**
 * JsonException automatically generated by SDKgen please do not edit this file manually
 * {@link https://sdkgen.app}
 */

import {KnownStatusCodeException} from "sdkgen-client"


export class JsonException extends KnownStatusCodeException {

    public constructor(private payload: any) {
        super('The server returned an error');
    }

    public getPayload(): any {
        return this.payload;
    }

}
