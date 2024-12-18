class Bookmark {
    constructor() {
        this.load();
    }

    load() {
        this.bookmarks = JSON.parse(localStorage.getItem('bookmarks')) ?? [];
    }

    save() {
        localStorage.setItem('bookmarks', JSON.stringify(this.bookmarks));
    }

    add(id) {
        this.bookmarks.push(id);
        this.save();
    }

    remove(id) {
        this.bookmarks = this.bookmarks.filter(item => item !== id);
        this.save();
    }

    isBookmarked(id) {
        return this.bookmarks.includes(id);
    }

    getList() {
        return this.bookmarks;
    }
}