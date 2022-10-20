# Web Puzzlers Triggers Changelog

## 2.1.3 - 2022-10-20
### Fixed
- Fixed error with missing import. [#8](https://github.com/ryssbowh/craft-triggers/issues/8)

## 2.1.2 - 2022-10-12
### Changed
- Changed plugin icons

## 2.1.1 - 2022-08-30
### Fixed
- Fixed error with Site condition

## 2.1.0 - 2022-08-24
### Changed
- Changed all deleted and saved elements conditions `Element::EVENT_AFTER_DELETE` to `Elements::EVENT_AFTER_DELETE_ELEMENT` to make sure elements are fully saved and propagated
### Added
- Related to entry condition
- Related to asset condition
- Related to user condition
- Related to category condition
- Related to product condition

## 2.0.7 - 2022-06-06
### Fixed
- Fixed wrong event class on elements deleted [4](https://github.com/ryssbowh/craft-triggers/issues/4)

## 2.0.6 - 2022-05-27
### Fixed
- "Draft" condition only applies to entries triggers
- "Revision" condition only applies to entries triggers
- "Log" action outputs the full trigger type

## 2.0.5 - 2022-05-27
### Fixed
- "Is new" condition now based on $entry->firstSave

## 2.0.4 - 2022-05-26
### Added
- Log action

## 2.0.3 - 2022-05-15
### Fixed
- Fixed issue with permissions

## 2.0.2 - 2022-05-14
### Fixed
- Issue with groups of conditions

## 2.0.1 - 2022-05-12
### Changed
- Changed EntryDraft, EntryRevision and EntrySlug handles

### Added
- Commerce triggers

## 2.0.0 - 2022-05-10
### Added
- Craft 4 support