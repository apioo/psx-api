const client = new Client()
client.foo.bar().get(): EntryCollection
client.foo.bar().create(payload: EntryCreate): EntryMessage throws EntryMessage
client.foo.baz().get(year: string): EntryCollection
client.foo.baz().create(payload: EntryCreate): EntryMessage
client.bar().find(foo: string): EntryCollection
client.bar().put(payload: EntryCreate): EntryMessage



interface EntryCollection {
    entry?: Array<Entry>
}

interface Entry {
    id?: number
    userId?: number
    title?: string
    date?: string
}

interface EntryMessage {
    success?: boolean
    message?: string
}

interface EntryCreate {
    id?: number
    userId?: number
    title?: string
    date?: string
}

