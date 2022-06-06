# Web Puzzlers Triggers Changelog

## 1.0.8 - 2022-06-06

### Fixed
- Fixed wrong event class on elements deleted [4](https://github.com/ryssbowh/craft-triggers/issues/4)

## 1.0.7 - 2022-05-27

### Fixed
- "Draft" condition only applies to entries triggers
- "Revision" condition only applies to entries triggers
- "Log" action outputs the full trigger type

## 1.0.6 - 2022-05-27

### Fixed
- "Is new" condition now based on $entry->firstSave

## 1.0.5 - 2022-05-26

### Added
- Log action

## 1.0.4 - 2022-05-14

### Fixed
- Issue with groups of conditions

## 1.0.3 - 2022-05-12

### Changed
- Changed EntryDraft, EntryRevision and EntrySlug handles

### Added
- Commerce triggers

## 1.0.2 - 2022-05-10

### Fixed
- Issue when saving groups within groups

## 1.0.1 - 2022-05-09

### Fixed
- Remove conditions that don't apply to trigger when changing trigger

## 1.0.0 - 2022-05-07

### Added
- Initial release