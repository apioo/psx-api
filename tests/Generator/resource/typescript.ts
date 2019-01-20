namespace FooNameType {
    export interface Endpoint {
        PathTemplate?: Path
        GetQuery?: Query
        Collection?: Collection
        Item?: Item
        Message?: Message
    }
    export interface Path {
        name: string
        type?: string
    }
    export interface Query {
        startIndex: number
        float?: number
        boolean?: boolean
        date?: string
        datetime?: string
    }
    export interface Collection {
        entry?: Array<Item>
    }
    export interface Item {
        id: number
        userId?: number
        title?: string
        date?: string
    }
    export interface Message {
        success?: boolean
        message?: string
    }
    
}
