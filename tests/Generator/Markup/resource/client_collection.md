const client = new Client()
client.foo.bar().get(): EntryCollection
client.foo.bar().create(payload: EntryCreate): EntryMessage throws EntryMessage
client.foo.baz().get(year: string): EntryCollection
client.foo.baz().create(payload: EntryCreate): EntryMessage
client.bar().find(foo: string): EntryCollection
client.bar().put(payload: EntryCreate): EntryMessage


export class EntryCollection {
    entry?: Array<Entry>
}

export class Entry {
    id?: number
    userId?: number
    title?: string
    date?: string
}

export class EntryMessage {
    success?: boolean
    message?: string
}

export class EntryCreate {
    id?: number
    userId?: number
    title?: string
    date?: string
}
