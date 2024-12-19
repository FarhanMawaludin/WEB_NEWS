class Bookmark {
    constructor() {
        this.load();
    }

    load() {
        let loadedStorage = JSON.parse(localStorage.getItem('bookmarks')) ?? {};
        this.bookmarks = Object.keys(loadedStorage)
                            .filter(key => key !== "length")
                            .reduce((acc, key) => {
                                acc[key] = loadedStorage[key];
                                return acc;
                            }, {});
        this.length = loadedStorage["length"] ?? 0;
    }

    save() {
        localStorage.setItem('bookmarks', JSON.stringify({
            ...this.bookmarks,
            "length": this.length ?? 0
        }));
    }

    add(
        id,
        title,
        summary,
        author,
        mediaUrl,
        mediaExt,
        date
    ){
        this.bookmarks[id] = {
            "title": title,
            "summary": summary,
            "author": author,
            "mediaUrl": mediaUrl,
            "mediaExt": mediaExt,
            "date": date
        };
        this.length++;
        this.save();
    }

    remove(id) {
        this.bookmarks = Object.keys(this.bookmarks)
                            .filter(item => item !== id)
                            .reduce((acc, key) => {
                                acc[key] = loadedStorage[key];
                                return acc;
                            }, {});
        this.length--;
        this.save();
    }

    isBookmarked(id) {
        return this.bookmarks.hasOwnProperty(id);
    }

    getListId() {
        return Object.keys(this.bookmarks);
    }
}